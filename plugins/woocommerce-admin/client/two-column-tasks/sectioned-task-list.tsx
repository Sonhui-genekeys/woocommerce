/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useRef, useState } from '@wordpress/element';
import { Panel, PanelBody, PanelRow } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import {
	updateQueryString,
	getHistory,
	getNewPath,
} from '@woocommerce/navigation';
import {
	OPTIONS_STORE_NAME,
	ONBOARDING_STORE_NAME,
	TaskType,
	getVisibleTasks,
	WCDataSelector,
} from '@woocommerce/data';
import { recordEvent } from '@woocommerce/tracks';
import { List, TaskItem } from '@woocommerce/experimental';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import '../tasks/task-list.scss';
import './sectioned-task-list.scss';
import TaskListCompleted from './completed';
import { TaskListProps } from '~/tasks/task-list';
import { ProgressHeader } from '~/task-lists/progress-header';
import { SectionPanelTitle } from './section-panel-title';

type PanelBodyProps = Omit< PanelBody.Props, 'title' | 'onToggle' > & {
	title: string | React.ReactNode | undefined;
	onToggle?: ( isOpen: boolean ) => void;
};
const PanelBodyWithUpdatedType = PanelBody as React.ComponentType< PanelBodyProps >;

export const SectionedTaskList: React.FC< TaskListProps > = ( {
	query,
	id,
	eventPrefix,
	tasks,
	keepCompletedTaskList,
	isComplete,
	sections,
	displayProgressHeader,
} ) => {
	const { createNotice } = useDispatch( 'core/notices' );
	const { updateOptions, dismissTask, undoDismissTask } = useDispatch(
		OPTIONS_STORE_NAME
	);
	const { profileItems } = useSelect( ( select: WCDataSelector ) => {
		const { getProfileItems } = select( ONBOARDING_STORE_NAME );
		return {
			profileItems: getProfileItems(),
		};
	} );
	const { hideTaskList } = useDispatch( ONBOARDING_STORE_NAME );
	const [ openPanel, setOpenPanel ] = useState< string | null >(
		sections?.find( ( section ) => ! section.isComplete )?.id || null
	);

	const prevQueryRef = useRef( query );

	const visibleTasks = getVisibleTasks( tasks );

	const recordTaskListView = () => {
		if ( query.task ) {
			return;
		}

		recordEvent( `${ eventPrefix }view`, {
			number_tasks: visibleTasks.length,
			store_connected: profileItems.wccom_connected,
		} );
	};

	useEffect( () => {
		recordTaskListView();
	}, [] );

	useEffect( () => {
		const { task: prevTask } = prevQueryRef.current;
		const { task } = query;

		if ( prevTask !== task ) {
			window.document.documentElement.scrollTop = 0;
			prevQueryRef.current = query;
		}
	}, [ query ] );

	const onDismissTask = ( taskId: string ) => {
		dismissTask( taskId );
		createNotice( 'success', __( 'Task dismissed' ), {
			actions: [
				{
					label: __( 'Undo', 'woocommerce-admin' ),
					onClick: () => undoDismissTask( taskId ),
				},
			],
		} );
	};

	const hideTasks = () => {
		hideTaskList( id );
	};

	const keepTasks = () => {
		const updateOptionsParams = {
			woocommerce_task_list_keep_completed: 'yes',
		};

		updateOptions( {
			...updateOptionsParams,
		} );
	};

	let selectedHeaderCard = visibleTasks.find(
		( listTask ) => listTask.isComplete === false
	);

	// If nothing is selected, default to the last task since everything is completed.
	if ( ! selectedHeaderCard ) {
		selectedHeaderCard = visibleTasks[ visibleTasks.length - 1 ];
	}

	const trackClick = ( task: TaskType ) => {
		recordEvent( `${ eventPrefix }_click`, {
			task_name: task.id,
		} );
	};

	const onTaskSelected = ( task: TaskType ) => {
		trackClick( task );
		if ( task.actionUrl ) {
			if ( task.actionUrl.startsWith( 'http' ) ) {
				window.location.href = task.actionUrl;
			} else {
				getHistory().push( getNewPath( {}, task.actionUrl, {} ) );
			}
			return;
		}

		updateQueryString( { task: task.id } );
	};

	const getSectionTasks = ( sectionTaskIds: string[] ) => {
		return visibleTasks.filter( ( task ) =>
			sectionTaskIds.includes( task.id )
		);
	};

	if ( ! visibleTasks.length ) {
		return <div className="woocommerce-task-dashboard__container"></div>;
	}

	if ( isComplete && ! keepCompletedTaskList ) {
		return (
			<>
				<TaskListCompleted
					hideTasks={ hideTasks }
					keepTasks={ keepTasks }
					twoColumns={ false }
				/>
			</>
		);
	}

	return (
		<>
			{ displayProgressHeader ? (
				<ProgressHeader taskListId={ id } />
			) : null }
			<div
				className={ classnames(
					`woocommerce-task-dashboard__container woocommerce-sectioned-task-list two-column-experiment woocommerce-task-list__${ id }`
				) }
			>
				<Panel>
					{ ( sections || [] ).map( ( section ) => (
						<PanelBodyWithUpdatedType
							key={ section.id }
							title={
								<SectionPanelTitle
									section={ section }
									tasks={ tasks }
									active={ openPanel === section.id }
								/>
							}
							opened={ openPanel === section.id }
							onToggle={ ( isOpen: boolean ) => {
								if ( ! isOpen && openPanel === section.id ) {
									recordEvent(
										`${ eventPrefix }section_closed`,
										{
											id: section.id,
											all: true,
										}
									);
									setOpenPanel( null );
								} else {
									if ( openPanel ) {
										recordEvent(
											`${ eventPrefix }section_closed`,
											{
												id: openPanel,
												all: false,
											}
										);
									}
									setOpenPanel( section.id );
								}
								if ( isOpen ) {
									recordEvent(
										`${ eventPrefix }section_opened`,
										{
											id: section.id,
										}
									);
								}
							} }
							initialOpen={ false }
						>
							<PanelRow>
								<List animation="custom">
									{ getSectionTasks( section.tasks ).map(
										( task ) => {
											const className = classnames(
												'woocommerce-task-list__item',
												{
													'is-complete':
														task.isComplete,
													'is-disabled':
														task.isDisabled,
												}
											);
											return (
												<TaskItem
													key={ task.id }
													className={ className }
													title={ task.title }
													completed={
														task.isComplete
													}
													expanded={
														! task.isComplete
													}
													content={ task.content }
													onClick={ () => {
														if (
															! task.isDisabled
														) {
															onTaskSelected(
																task
															);
														}
													} }
													onDismiss={
														task.isDismissable
															? () =>
																	onDismissTask(
																		task.id
																	)
															: undefined
													}
													action={ () => {} }
													actionLabel={
														task.actionLabel
													}
												/>
											);
										}
									) }
								</List>
							</PanelRow>
						</PanelBodyWithUpdatedType>
					) ) }
				</Panel>
			</div>
		</>
	);
};

export default SectionedTaskList;
