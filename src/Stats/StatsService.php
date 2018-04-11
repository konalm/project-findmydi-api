<?php 

namespace App\Stats;

class StatsService 
{
    /**
     * 
     */
    public function validate_new_stat($stat) {
      if (!$stat->event) {
        return 'event is required'; 
      }

      if (!$stat->instructor_id) {
          return 'instructor id is required';
      }
    }
}