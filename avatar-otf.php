<?php
/**
 * Plugin Name: Avatar OTF
 * Description: Resize avatars on the fly
 * Version: 0.0.1
 * Author: Rafael Nowrotek
 * Author URI: https:/benignware.com
 */

require 'lib.php';

use function benignware\avatar_otf\get_image_file;
use function benignware\avatar_otf\get_src_dir;
use function benignware\avatar_otf\get_dest_dir;
use function benignware\avatar_otf\get_image_url;

add_filter('get_avatar_url', function ($url, $id_or_email = null, $args = []) {
	$src_path = get_image_file($url, true);

	// echo $src_path;

	if ($src_path) {
		$src_dir = get_src_dir();
		$dest_dir = get_dest_dir();
		
		[
			'dirname' => $dirname,
			'filename' => $filename,
			'extension' => $extension
		] = pathinfo($src_path);
		$width = $args['width'];
		$height = $args['height'];
		$crop = true;
		$dirname = ltrim($dirname, './');
		$filename = preg_replace('/\d+-\d+$/', '', $filename);
		$dest_path = ($dirname ? "$dirname/" : '') . "$filename-{$width}x{$height}.$extension";
		$src_file = $src_dir . '/' . $src_path;
		$dest_file = $dest_dir . '/' . $dest_path;

		$dest_file_dir = dirname($dest_file);

		if (!is_dir($dest_file_dir)) {
			mkdir($dest_file_dir, 0777, true);
		}

		if (!file_exists($dest_file) || filemtime($src_file) > filemtime($dest_file)) {
			// echo 'GENERATE FROM : ' . $src_file . '<br/>';
			// echo 'SAVE TO : ' . $dest_file . '<br/>';
			

			// $ret = image_resize( $src_file, $width, $height, $crop = false, $suffix = null, 'avatar-otf/xxx-png');

			// echo $ret;

			// if (is_a($ret, 'WP_Error')) {
			// 	echo 'ERROR';
			// 	print_r($ret);
			// 	return $url;
			// } else {
			// 	echo 'NO ERR';
			// }
			$image_editor = wp_get_image_editor($src_file);
			$image_editor->resize($width, $height, true);
			$image_editor->set_quality( 100 );
			$ret = $image_editor->save($dest_file);
		}

		if (file_exists($dest_file)) {
			$url = get_image_url($dest_file);
		}

	}

	return $url;
}, 1000, 3);
