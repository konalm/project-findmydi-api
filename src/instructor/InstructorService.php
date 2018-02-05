<?php 

namespace App\Instructor;

use App\Services\TokenService; 
use App\Instructor\InstructorRepo;


class InstructorService
{
    public function __construct(\Slim\Container $container) {
      $this->repo = new InstructorRepo($container);
      $this->token_service = new TokenService();
    }

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

      if (intval($request->getParam('status')) === 0 && 
        !$request->getParam('rejectReason')
      ) {
        return 'reason is required when rejecting adi licence';
      }

      return false;
    }

    /**
     * check specific instructor credentials to check if they have become verifed
     */
    public function check_verified($token_instructor) {
      if (!$token_instructor) { return false; }
      if ($token_instructor->verified) { return false; }

      $instructor = $this->repo->get($token_instructor->id); 
      $coverages = json_decode($instructor['coverages']);

      if (!$instructor['hourly_rate']) {
        return 'verification failed, no hourly rate';
      }

      if (!sizeof(array_filter($coverages)) > 0) {
        return 'verification failed, at least one coverage is required';
      }

      if (!$instructor['avatar_url']) {
        return 'verification failed, avatar required';
      }

      if (!$instructor['adi_licence_verification']) {
        return 'verification failed, adi licence upload not verified';
      }

      $this->repo->update_verified($token_instructor->id, true);
    } 
}