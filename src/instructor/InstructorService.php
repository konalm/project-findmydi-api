<?php 

namespace App\Instructor;

use App\Services\TokenService; 
use App\Instructor\InstructorRepo;


class InstructorService
{
    public function __construct(\Slim\Container $container) {
      $this->container = $container;
      $this->repo = new InstructorRepo($container);
      $this->token_service = new TokenService();
    }

    /**
     * check if instructor has all necassery data to of completed induction 
     */
    public function check_instructor_inducted($instructor) {
      error_log('check instructor inducted');

      if (!$instructor['intro_read']) { 
        error_log('intro not read');
        return; 
      }

      if (!$instructor['hourly_rate']) {
        error_log('no hourly rate');
        return; 
      }

      $coverages = json_encode($instructor['coverages']);
      if (sizeof($coverages) === 0) {
        error_log('no coverages');
        return;
      }

      if (!$instructor['avatar_url']) {
        error_log('no avatar');
        return;
      }

      if (!$instructor['adi_licence_no']) {
        error_log('no adi licence no');
        return;
      }

      if (!$instructor['adi_licence_verification']) {
        return; 
      }

      error_log('criteria met for induction passed !!');
      return true;
    }

    /**
     * validate adi licence data to be uploaded
     */
    public function adi_licence_validation ($licence_data) {
      if (!$licence_data->no) {
        return 'adi licence number is required';
      }

      if (!$licence_data->img) {
        return 'adi licence image is required';
      }
    }

    /**
     * 
     */
    public function build_adi_img_src($id) {
      return 
        $this->container->getUploadDir . 'adiLicenceVerification/' . $id . '.jpg';
    }
    
    /**
     * 
     */
    public function validate_hourly_rate($hourly_rate) {
      if (!$hourly_rate) {
        return 'hourly rate is required';
      }

      if (intval($hourly_rate <= 0)) {
        return 'hourly rate must be greater than 0';
      }
    }

    /**
     *  validate intro read update
     */
    public function validate_intro_read_update($read_status) {
      if (!$read_status) {
        return 'read status is required';
      }

      if (
        strtolower($read_status) !== 'true' && 
        strtolower($read_status !== 'false')
      ) {
        // return 'read status is incorrect format';
      }
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
      if (!$instructor->first_name) {
        return 'first name is required';
      }

      if (!$instructor->surname) {
        return 'surname is required';
      }

      if (!$instructor->email) {
        return 'email is required';
      }

      if (!$instructor->contact_number) { 
        return 'contact number is required'; 
      }

      if (!$instructor->hourly_rate) { 
        return 'hourly rate is required'; 
      }

      if (!is_numeric($instructor->hourly_rate)) {
        return 'hourly rate must be a number';
      }

      if ($instructor->hourly_rate <= 0) {
        return 'hourly rate must be greater than 0';
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