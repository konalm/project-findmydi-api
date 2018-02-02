<?php 

namespace App\Instructor;

class InstructorService
{
    /**
     * valdate instructor input recieved from request
     */
    public function validate_instructor_details($instructor) {
      if (!$instructor->first_name) { return 'first name is required'; }
      if (!$instructor->surname) { return 'surname is required'; }
      if (!$instructor->email) { return 'email is required'; }
      if (!$instructor->adi_license_no) { return 'adi license no is required'; }
      if (!$instructor->gender) { return 'gender is required'; }
      if (!$instructor->password) { return 'password is required'; }

      return false;
    }

    /**
     * validate instructor input for updating profile
     */
    public function validate_instructor_profile($instructor) {
      if (!$instructor->hourly_rate) { 
        return 'hourly rate is required'; 
      }

      if (!is_numeric($instructor->hourly_rate)) {
        return 'hourly rate must be a number';
      }

      if ($instructor->hourly_rate <= 0) {
        return 'hourly rate must be greater than 0';
      }

      if (!$instructor->contact_number) { 
        return 'contact number is required'; 
      }

      if (!is_numeric($instructor->contact_number)) {
        return 'contact number must be a number';
      }

      return false;
    }

    /**
     * validate adi licence status update 
     */
    public function validate_adi_licence_status_update($request) {
      if ($request->getParam('status') === null) {
        return 'status is required';
      }

      if (intval($request->getParam('status')) === 1 && 
        !$request->getParam('rejectReason')
      ) {
        return 'reason is required when rejecting adi licence';
      }

      return false;
    }
}