<?php
class SimpleAutoPOP_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'description' => 'Simple AutoPOP',
        );
        parent::__construct( 'simple_autopop_widget', 'Simple AutoPOP', $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $eventProvider = Simple_AutoPOP::get_event_provider();
        $eventProvider->setEventNumber($instance['eventCount'] ? $instance['eventCount'] : 3);
        $eventGenerator = new Simple_AutoPOP_EventGenerator(get_option(Simple_AutoPOP::PREFIX . '_activate_links', 0));
        echo $eventGenerator->generateFrom($eventProvider->getEvents(), true);
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        $eventCount = ! empty( $instance['eventCount'] ) ? $instance['eventCount'] : "3";
        // outputs the content of the widget
        echo '
        <label>
            Number of events   
            <input type="number" name="'.esc_attr( $this->get_field_name( 'eventCount' ) ).'" value="'.$eventCount.'">
        </label>
        ';
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['eventCount'] = ( ! empty( $new_instance['eventCount'] ) ) ? strip_tags( $new_instance['eventCount'] ) : 3;
        update_option(Simple_AutoPOP_EventCacher::getCacheKey($instance['eventCount']), false);

        return $instance;
    }
}
