<?php

namespace Lagostechboy\EditorialWorkflow\Workflow;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class StateMachine
 * 
 * Defines allowed state transitions for content review workflow.
 */
class StateMachine {

    /**
     * Lists of allowed transitions.
     * 
     * Format: current_state => [allowed_next_states]
     * 
     * @var array
     */
    private $transitions = [
        'draft'          => ['pending_review'],
        'pending_review' => ['publish', 'draft'], //draft = rejected, publish = approved
        'approved'       => ['publish'],
    ];

    /**
     * Check if a transition is allowed.
     * 
     * @param string $currentState
     * @param string $nextState
     * 
     * @return bool
     */
    public function canTransition( string $currentState, string $nextState): bool {
        if ( ! isset( $this->transitions[ $currentState ])) {
            return false;
        }

        return in_array( $nextState, $this->transitions[ $currentState ], true);
    
    }
}