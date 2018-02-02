<?php 

namespace App\Controllers;

use \Interop\Container\ContainerInterface as ContainerInterface;


class ImgController 
{
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * return image response of adi license photo for user
   */
  public function get_adi_licence_photo($request, $response, $args) {
    $img = file_get_contents(
      __DIR__ . '/../uploads/adiLicenceVerification/' . $args['user_id'] . '.jpg'
    );

    $response->write($img);
    return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
  }

  /**
   * return image response of instructor avatar
   */
  public function get_instructor_avatar($request, $response, $args) {
    $img = file_get_contents(
      __DIR__ . '/../uploads/instructorAvatar/' . $args['user_id'] . '.jpg'
    );

    $response->write($img);
    return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
  }

  /**
   * serve image response 
   */
  public function serve_image($request, $response, $args) {
    $path = $args['path'];
    $img = file_get_contents(__DIR__ . '/../' . $path);

    $response->write($img);
    return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
  }
}