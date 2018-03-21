<?php

namespace App\Services;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Slim\Http\UploadedFile;
use App\Services\TokenService;


class UserService
{
  public function __construct(\Slim\Container $container) {
    $this->container = $container; 
    $this->token_service = new TokenService();
  }

  /**
   * grab instructor adi license and store it in uploads 
   */
  public function upload_adi_license_photo($request) {
    $uploaded_files = $request->getUploadedFiles();
    $adi_license_photo = $uploaded_files['adiLicensePhoto'];

    $user = $this->token_service->get_decoded_user($request);
    $extension = pathinfo($adi_license_photo->getClientFilename(), PATHINFO_EXTENSION);
    $adi_license_photo_name = $user->id . '.jpg';

    $move_to_dir = $this->container->getUploadDir . 
      'adiLicenseVerification/' . 
      $adi_license_photo_name;

    $adi_license_photo->moveTo($move_to_dir);
  }

  /**
   * send postcode to api.postcode.io to check postcode is valid
   * and return the longitude and latitude
   */
  public function get_long_and_lat($postcode) {
    $client = new \GuzzleHttp\Client();

    try {
      $res = $client->request(
        'GET', 
        "http://api.postcodes.io/postcodes/${postcode}"
      );
    } catch (RequestException $e) {
      return false;
    }

    return json_decode($res->getBody());
  }
}