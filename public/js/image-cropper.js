let originalImage = null;
let croppedImageDataUrl = null;
let isDragging = false;
let startedDragging = false;
let isMovingCrop = false;
let cropStartX, cropStartY, cropEndX, cropEndY;
let cropOldX, cropOldY;
let cropOffsetX = 0, cropOffsetY = 0; // For moving the crop area
const aspectRatio = 16 / 9; // fixed aspect ratio for crop area.
let canvas = document.getElementById('imageCanvas');

let imageWidth;
let imageHeight;

// Event listener to open the image popup when a file is selected.
document.getElementById('imageInput').addEventListener('change', function() {
    openImagePopup();
})

// Event listener to resize canvas on window resize.
window.addEventListener('resize', function () {
    if (originalImage) {
        resizeCanvas();
        resetCanvas();
        initializeDefaultCropArea();
    }
})

// Calculate mouse position relative to canvas.
function getMousePos(e) {
    let rect = canvas.getBoundingClientRect();
    return {
        x: (e.clientX - rect.left) * (canvas.width / rect.width),
        y: (e.clientY - rect.top) * (canvas.height / rect.height)
    };
}

// Function to resize the canvas based on window size and dimensions.
function resizeCanvas() {
    const maxWidth = Math.min(900, window.innerWidth*0.97);
    const maxHeight = Math.min(600, window.innerHeight*0.8);
    // Calculate width difference ratio
    let ratio = maxWidth / imageWidth;
    if (imageHeight  * ratio > maxHeight) {
        // Adjust the ratio based on height if needed.
        ratio = maxHeight / imageHeight;
    }

    // set size of the container of the canvas.
    canvas.style.height = (ratio*imageHeight).toString()+"px";
    canvas.style.width = (ratio*imageWidth).toString()+"px";
}

// Opens the image popup and sets up canvas.
function openImagePopup() {
    const imageInput = document.getElementById('imageInput');
    // Check if it actually contains a file.
    if (imageInput.files && imageInput.files[0]) {
        // create a reader to grab the image.
        const reader = new FileReader();
        reader.onload = function(e) {
            // create a new Image container for the file
            const img = new Image();
            if (typeof e.target.result === 'string') {
                img.src = e.target.result;
            }

            img.onload = function() {
                canvas = document.getElementById('imageCanvas');
                // Save the image size for resizing the canvas, while keeping the aspect ratio.
                imageWidth = img.width;
                imageHeight = img.height;
                resizeCanvas();

                // set the size of the inside of the canvas.
                canvas.width = img.width;
                canvas.height = img.height;
                originalImage = img;

                // Draw the image and initial crop rectangle
                resetCanvas();
                initializeDefaultCropArea();
                document.getElementById('imagePopup').classList.add('active');


                // Add mouse events for selecting and moving the crop area
                canvas.onmousedown = startAction;
                canvas.onmousemove = duringAction;
                canvas.onmouseup = endAction;
            };
        };
        reader.readAsDataURL(imageInput.files[0]);
    }
}

// Initializes the default crop area based on image size.
function initializeDefaultCropArea() {
    let defaultCropWidth;
    let defaultCropHeight;
    if (canvas.height * aspectRatio > canvas.width) {
        defaultCropWidth = canvas.width;
        defaultCropHeight = defaultCropWidth / aspectRatio;
    } else {
        defaultCropHeight = canvas.height;
        defaultCropWidth = defaultCropHeight * aspectRatio;
    }

    // Crop area is 90% of maximum size
    cropStartX = (canvas.width - defaultCropWidth * 0.9) / 2;
    cropStartY = (canvas.height - defaultCropHeight * 0.9) / 2;
    cropEndX = cropStartX + defaultCropWidth * 0.9;
    cropEndY = cropStartY + defaultCropHeight * 0.9;

    drawCropArea();
}

// Resets the canvas, clears previous drawings, and draws a new area.
function resetCanvas() {
    const ctx = canvas.getContext('2d');

    // Clear the canvas and redraw the image
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(originalImage, 0, 0);

    drawCropArea();
}

// Draws the crop rectangle on the canvas
function drawCropArea() {
    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = 'red';
    ctx.lineWidth = 2;
    ctx.strokeRect(cropStartX, cropStartY, cropEndX - cropStartX, cropEndY - cropStartY);
}

// Starts the action for dragging or moving the crop area based on mouse position.
function startAction(e) {
    const pos = getMousePos(e);

    const minX = Math.min(cropStartX, cropEndX);
    const maxX = Math.max(cropStartX, cropEndX);
    const minY = Math.min(cropStartY, cropEndY);
    const maxY = Math.max(cropStartY, cropEndY);

    // check if mouse is inside the rectangle.
    if (pos.x > minX && pos.x < maxX && pos.y > minY && pos.y < maxY) {
        conditionalSwap();
        isMovingCrop = true;
        cropOffsetX = pos.x - cropStartX;
        cropOffsetY = pos.y - cropStartY;
    } else {
        // Start a new crop area
        cropOldX = pos.x;
        cropOldY = pos.y;
        isDragging = true;
        startedDragging = false;
        isMovingCrop = false;
    }
}

function cropWidth() {
    return Math.abs(cropEndX - cropStartX);
}
function cropHeight() {
    return Math.abs(cropEndY - cropStartY);
}
// expected width when the crop height is defined.
function cropExpectedWidth() {
    return cropHeight() * aspectRatio;
}
// expected height when the crop width is defined.
function cropExpectedHeight() {
    return cropWidth() / aspectRatio;
}

// Shrink the specified vertex based its counterpart.
function shrinkOtherDimension(vertical) {
    if (vertical) {
        let expectedHeight = cropExpectedHeight();
        if (cropEndY <= cropStartY) expectedHeight *= -1;
        cropEndY = cropStartY + expectedHeight;
    } else {
        let expectedWidth = cropExpectedWidth();
        if (cropEndX <= cropStartX) expectedWidth *= -1;
        cropEndX = cropStartX + expectedWidth;
    }

}
// Check if the borders of the crop area are inside bounds,
// and shrink them if needed.
function shrinkToBorders() {
    if (cropEndX > cropStartX) {
        if (cropEndX > canvas.width) {
            cropEndX = canvas.width;
            shrinkOtherDimension(true);
        }
    } else {
        if (cropEndX < 0) {
            cropEndX = 0;
            shrinkOtherDimension(true);
        }
    }

    if (cropEndY > cropStartY) {
        if (cropEndY > canvas.height) {
            cropEndY = canvas.height;
            shrinkOtherDimension(false);
        }
    } else {
        if (cropEndY < 0) {
            cropEndY = 0;
            shrinkOtherDimension(false);
        }
    }
}

// Call the function based on mouse position and previous action.
function duringAction(e) {
    const pos = getMousePos(e);

    if (isDragging) {
        handleDragging(pos.x, pos.y);
    } else if (isMovingCrop) {
        handleMoving(pos.x, pos.y);
    }

    // Redraw the canvas, and the crop area after any movement
    resetCanvas();
}

// draws a new square size is determined by the horizontal vertex.
function handleDragging(mouseX, mouseY) {
    if (startedDragging === false) {
        cropStartX = cropOldX;
        cropStartY = cropOldY;
        startedDragging = true;
    }
    createSquare(mouseX, mouseY);
}

// moves the square over based on the pixels moved from starting offset.
function handleMoving(mouseX, mouseY) {
    // Move the existing crop area.
    const newCropStartX = mouseX - cropOffsetX;
    const newCropStartY = mouseY - cropOffsetY;

    // Calculate the new crop end coordinates while keeping the 16:9 aspect ratio.
    const cropWidth = cropEndX - cropStartX;
    const cropHeight = cropEndY - cropStartY;
    cropEndX = newCropStartX + cropWidth;
    cropEndY = newCropStartY + cropHeight;

    // make sure the area can't move outside the borders.
    if (newCropStartX < 0) {
        cropEndX -= newCropStartX;
        cropStartX = 0;
    } else if (cropEndX > canvas.width) {
        const diff = cropEndX - canvas.width;
        cropEndX = canvas.width;
        cropStartX = newCropStartX - diff;
    } else {
        cropStartX = newCropStartX;
    }
    if (newCropStartY < 0) {
        cropEndY -= newCropStartY;
        cropStartY = 0;
    } else if (cropEndY > canvas.height) {
        const diff = cropEndY - canvas.height;
        cropEndY = canvas.height;
        cropStartY = newCropStartY - diff;
    } else {
        cropStartY = newCropStartY;
    }
}

// Swaps the end and start coordinates if start is a higher number.
function conditionalSwap() {
    if (cropStartX > cropEndX) {
        const temp = cropStartX;
        cropStartX = cropEndX;
        cropEndX = temp;
    }
    if (cropStartY > cropEndY) {
        const temp = cropStartY;
        cropStartY = cropEndY;
        cropEndY = temp;
    }
}
// create the new square based on the new x and y position.
function createSquare(newX, newY) {
    const cropWidth = newX - cropStartX;
    let cropHeight = Math.abs(cropWidth / aspectRatio);
    if (newY < cropStartY) cropHeight *= -1;

    cropEndX = cropStartX + cropWidth;
    cropEndY = cropStartY + cropHeight;

    shrinkToBorders();
}

// Finishes the action and resets variables.
function endAction() {
    if (startedDragging === false && isDragging === true) {
        createSquare(cropOldX, cropOldY)
    }
    isDragging = false;
    startedDragging = false;
    isMovingCrop = false;

    resetCanvas();
}

// Creates a final image after cropping to the defined square.
function cropImage() {

    // Calculate the final crop width and height
    const cropWidth = cropEndX - cropStartX;
    const cropHeight = cropWidth / aspectRatio;

    // Create a new canvas to store the cropped image
    const croppedCanvas = document.createElement('canvas');
    croppedCanvas.width = cropWidth;
    croppedCanvas.height = cropHeight;
    const croppedCtx = croppedCanvas.getContext('2d');

    // Draw the original image (not the canvas with the red square)
    croppedCtx.drawImage(
        originalImage,
        cropStartX, cropStartY, cropWidth, cropHeight,
        0, 0, cropWidth, cropHeight
    );

    // Compress the cropped image
    croppedImageDataUrl = croppedCanvas.toDataURL('image/jpeg', 0.8);

    document.getElementById('imagePopup').classList.remove('active');

    // Set the cropped image as a hidden input to send it to the server.
    document.getElementById('croppedImageInput').value = croppedImageDataUrl;
}
