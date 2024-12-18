<?php

use App\Responses\RedirectResponse;
use App\Responses\HtmlResponse;
use JetBrains\PhpStorm\NoReturn;

/**
 * Constructs a file path relative to the project root by joining the provided path segments.
 *
 * <br>Example of `segments`:
 *  * ['public'] would route to '/public'
 *  * ['public', 'index.php'] becomes '/public/index.php'
 * @param string ...$segments One or more path segments to join into a path
 * @return string The resulting path.
 */
function path(string ...$segments): string
{
	return __DIR__ . '/../' . implode('/', $segments);
}


/**
 * Provides the current PDO instance connected to the database.
 *
 * @return PDO The connection to the database.
 */
function conn(): PDO {
	static $pdo = null;

	if ($pdo === null) {
		/** @var PDO $pdo */
		$pdo = require __DIR__ . '/../db.php';
	}

	return $pdo;
}

/**
 * Redirects to specified route by name.
 *
 * The function generates a URL using the route name,
 * returning a RedirectResponse on success. If the generation fails
 * due to an exception (such as missing route), it redirects to home.
 *
 * <br>Example of the route string:
 *  * "profile.show" would use the `show` method in the `profile` controller.
 *
 * Example of `params`:
 *  * ['id' => $user] would make the var $user available as $id in the view.
 *
 * @param string $routeName The name of the route to which the response should redirect.
 * @param array $params Parameters to be used to map variables in the route.
 * @return RedirectResponse The response instance containing information for the redirect.
 */
function redirect(string $routeName, array $params = []): RedirectResponse
{
	$router = Router::getRouter();
	try {
		$url = $router->routeUrl($routeName, $params);
		// Successful generation, return Redirect.
		return new RedirectResponse($url);
	}
	catch (Exception $e) {
		// Generation failed, return to home.
		$redirect = new RedirectResponse('/');
		return $redirect->with('redirect', 'Invalid redirect, going to home instead.');
	}
}

/**
 * Creates a rendered view wrapped in an HtmlResponse object.
 *
 * The function constructs a view using a specified path and optional data.
 * It supports hierarchical view structures divided by '.' dots.
 * A different template can be defined at the start of the string followed by a '/' forward slash.
 * The function expects a view string of formatted as 'template/subdir.view'.
 *
 * <br>Example of the view string:
 *  * "profile.show" would use the `show` view under the `profile` directory.
 *  * "form/profile.create" would use the `create` view under the `profile` directory using the form template.
 *
 * @param string $viewString The path to the view, optionally containing a template and subdirectories.
 * @param array $data An associative array of data to pass to the view.
 * @return HtmlResponse Returns an HtmlResponse containing the rendered view or error message.
 */
function view(string $viewString, array $data = []): HtmlResponse
{
	// Splits the string into [template, view] or [view]
	$parts = explode('/', $viewString);
	// Extract the view from the last arg of parts
	$view = array_pop($parts);

	// Check if a template is defined.
	if (!empty($parts)) {
		$template = array_pop($parts);
	} else {
		$template = 'default';
	}

	// replace dots with slashes to allow nested directories.
	$view = str_replace('.', '/', $view);

	$viewPath = path('app', 'views', $view . '.view.php');
	$templatePath = path('templates', $template . '.php');

	if (file_exists($templatePath) && file_exists($viewPath)) {
		extract ($data); // Make array keys available as variable names.
		ob_start(); // Start output buffering
		include $templatePath; // Include the template, containing the view.
		$content = ob_get_clean();  // Capture clean buffer
		return new HtmlResponse($content);
	}
	return new HtmlResponse('Page not found', 404);
}

/**
 * Saves a base64-encoded image to the server and returns its public URL.
 *
 * The function decodes a base64-encoded JPEG image string and stores it as a file in the /public/uploads directory.
 * The image is assigned a unique name to avoid overwriting. An optional aspect ratio (e.g., 16/9) can be provided to
 * crop the image to content from the center. Otherwise, the image is saved as is. If the directory does not exist yet,
 * they will be created.
 *
 * <br>Example usage of $aspectRatio:
 *  * 16/9 would crop the image to this aspect ratio.
 *
 * @param string $formImage The base64-encoded image string, prefixed with 'data:image/jpeg;base64'.
 * @param float|null $aspectRatio The desired ratio to crop the image to, if null, the image won't be cropped.
 * @return string Returns the public URL path to the saved image.
 */
function saveImage(string $formImage, ?float $aspectRatio = null): string {
	// Remove image prefix and replace spaces with pluses.
	$formImage = str_replace('data:image/jpeg;base64,' , '', $formImage);
	$formImage = str_replace(' ', '+', $formImage);

	$decodedImage = base64_decode($formImage);

	$imageFileName = uniqid() . '.jpg';

	// Generate private server path for storing the image.
	$privateImagePath = path('public', 'uploads', 'projects', $imageFileName);

	if (!file_exists(dirname($privateImagePath))) {
		// Create the directory if it does not exist. (recursive to generate nested files)
		mkdir(dirname($privateImagePath), 0777, true);
	}

	$imageResource = imagecreatefromstring($decodedImage);

	// Check if the image needs to be cropped
	if ($aspectRatio !== null && $imageResource !== false) {
		// Get original image width+height
		$originalWidth = imagesx($imageResource);
		$originalHeight = imagesy($imageResource);

		$expectedHeight = $originalWidth / $aspectRatio;

		if ($expectedHeight > $originalHeight) {
			$expectedHeight = $originalHeight;
			$expectedWidth = $originalHeight * $aspectRatio;
		}
		else {
			$expectedWidth = $originalWidth;
		}

		// Crop Start coordinates
		$cropX = ($originalWidth - $expectedWidth) / 2;
		$cropY = ($originalHeight - $expectedHeight) / 2;

		// Create a new image with expected dimensions.
		$croppedImage = imagecreatetruecolor($expectedWidth, $expectedHeight);

		imagecopyresampled
			(// (empty) Destination image to copy to.
			$croppedImage,
			// Source image to copy from.
			$imageResource,
			// Top left of image.
			0,0,
			// Coordinates where cropping starts.
			$cropX, $cropY,
			// Coordinates of the destination image.
			$expectedWidth, $expectedHeight,
			// Width and height of the source image to copy. Same as destination size for cropping.
			$expectedWidth, $expectedHeight
		);

		$imageResource = $croppedImage;
	}

	// save image on the server at the specified path.
	imagejpeg($imageResource, $privateImagePath);

	// destroy the image for memory.
	imagedestroy($imageResource);

	// Returns the public URL path for the saved image.
	return '/public/uploads/projects/' . $imageFileName;
}

/**
 * Aborts the current execution and displays an error message.
 *
 * This function provides a way to handle unexpected errors, immediately stopping further processing.
 * Intended for cases where a non-recoverable error occurs.
 * 
 * @param string $message A message describing the error, which will be displayed on the screen.
 * @return void This function terminates after execution.
 */
#[NoReturn] function abort(string $message): void
{
	$title = "Unexpected Error";
	include("templates/error.php");
	exit;
}

/**
 * Get the error message associated with the field if any.
 *
 * This function provides the first error found for the specified field when validating.
 * If a value is found for the field, it is retrieved and removed from the session after.
 *
 * @param string $field The name of the post field
 * @return string|null The message encapsulated in a text field.
 */
function flashError(string $field): ?string
{
	/** @var ?string $value */
	$value = flash('errors', $field);
	if ($value !== null) {
		return "<p>$value[0]";
	}
	return null;
}

/**
 * Retrieves old input data for a specified form field if available.
 *
 * This function provides the values of the specified form field before posting.
 * If a value is found for the field, it is retrieved and removed from the session after.
 *
 * @param string $field The name identifier of the form field, same as is $_POST name.
 * @param string|null $alt An optional fallback value to be returned if no value exists.
 * @return string|null Returns the previous value for the field if found, otherwise the fallback value or null.
 */
function old(string $field, ?string $alt = null): ?string
{
	return flash('old', $field) ?? $alt;
}

/**
 * Retrieves and removes a flash message from the session.
 *
 * This function accesses a specified flash message stored in the session, optionally from a nested field.
 * If the specified flash message exists, it is returned and removed from the session, if not `null` is returned.
 * Automatically starts a session if one is not already started.
 *
 * <br>Example usage:
 *  * `flash('errors', 'name')`, retrieves and removes the errors for the “name” form field.
 *  * `flash('success')`, retrieves and removes a top-level flash success message.
 *
 * @param string $field The primary field to access in the flash session array.
 * @param string|null $nestedField The optional nested field to retrieve within the main $field array.
 * @return mixed The value of the flash message if it exists, otherwise `null`.
 */
function flash(string $field, ?string $nestedField = null): mixed
{
	if (session_status() === PHP_SESSION_NONE) {
		// Start session if it hasn't started yet.
		session_start();
	}
	if ($nestedField) {
		if (isset($_SESSION['flash'][$field][$nestedField])) {
			$value = $_SESSION['flash'][$field][$nestedField];
			unset($_SESSION['flash'][$field][$nestedField]);
			return $value;
		}
	} else {
		if (isset($_SESSION['flash'][$field])) {
			$value = $_SESSION['flash'][$field];
			unset($_SESSION['flash'][$field]);
			return $value;
		}
	}
	return null;
}

/**
 * Retrieves the authenticated user based on the session-stored user ID.
 *
 * This function checks if the session is associated with a user by verifying the existence of session `user_id`.
 * If a `user_id` is found, it attempts to retrieve its `User` model.
 * Automatically starts a session if one hasn't already started.
 *
 * @return App\Models\User|Null The authenticated `User` instance if found, otherwise `null`.
 */
function auth(): ?App\Models\User
{
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	return isset($_SESSION['user_id']) ? App\Models\User::find($_SESSION['user_id']) : null;
}