let originalImage = null;
let croppedImageDataUrl = null;
let isDragging = false;
let startedDragging = false;
let isMovingCrop = false;
let cropStartX, cropStartY, cropEndX, cropEndY;
let cropOldX, cropOldY;
let cropOffsetX = 0, cropOffsetY = 0; // For moving the crop area
const aspectRatio = 16 / 9;
let canvas = document.getElementById('imageCanvas');

let imageWidth;
let imageHeight;

document.getElementById('imageInput').addEventListener('change', function() {
    openImagePopup();
})

window.addEventListener('resize', function () {
    if (originalImage) {
        resizeCanvas();
        resetCanvas();
        initializeDefaultCropArea();
    }
})

function getMousePos(canvas, evt) {
    let rect = canvas.getBoundingClientRect();
    return {
        x: (evt.clientX - rect.left) * (canvas.width / rect.width),
        y: (evt.clientY - rect.top) * (canvas.height / rect.height)
    };
}

function resizeCanvas() {
    let dif;
    // inner width example: 1000px;
    const maxWidth = Math.min(900, window.innerWidth*0.97);
    const maxHeight = Math.min(600, window.innerHeight*0.8);
    // 1000 / 500 => 2.0  1000 / 2000 => 0.5
    dif = maxWidth / imageWidth;
    if (imageHeight  * dif > maxHeight) {
        dif = maxHeight / imageHeight;
    }
    /*if (img.height*1.2 > img.width) {
        // height at least 1.2 times bigger than width
        const base = Math.min(1000, window.innerHeight*0.9);
        dif = base/img.height;
    }
    else {
        const base = Math.min(500, window.innerWidth);
        dif = base/img.width;
    }*/

    // alert('w: ' + maxWidth + ' h: ' + maxHeight + ' d: ' + dif + '\n' + 'nw: ' + dif*imageWidth + ' nh: ' + dif*imageHeight);
    canvas.style.height = (dif*imageHeight).toString()+"px";
    canvas.style.width = (dif*imageWidth).toString()+"px";
}

function openImagePopup() {
    const imageInput = document.getElementById('imageInput');
    if (imageInput.files && imageInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            if (typeof e.target.result === 'string') {
                img.src = e.target.result;
            }

            img.onload = function() {


                canvas = document.getElementById('imageCanvas');
                imageWidth = img.width;
                imageHeight = img.height;
                resizeCanvas();

                canvas.width = img.width;
                canvas.height = img.height;
                originalImage = img;

                // Draw the image and initial crop rectangle
                resetCanvas();
                initializeDefaultCropArea();
                document.getElementById('imagePopup').classList.add('active');


                // document.getElementById('imagePopup').style.display = 'flex';

                // Add mouse events for selecting and moving the crop area
                canvas.onmousedown = startAction;
                canvas.onmousemove = duringAction;
                canvas.onmouseup = endAction;
            };
        };
        reader.readAsDataURL(imageInput.files[0]);
    }
}

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

    cropStartX = (canvas.width - defaultCropWidth) / 2;
    cropStartY = (canvas.height - defaultCropHeight) / 2;
    cropEndX = cropStartX + defaultCropWidth*0.9;
    cropEndY = cropStartY + defaultCropHeight*0.9;

    drawCropArea();
}

function resetCanvas() {
    const ctx = canvas.getContext('2d');

    // Clear the canvas and redraw the image
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(originalImage, 0, 0);

    // Redraw the crop rectangle
    drawCropArea();
}

function drawCropArea() {
    const ctx = canvas.getContext('2d');

    // Draw the cropping rectangle
    ctx.strokeStyle = 'red';
    ctx.lineWidth = 2;
    ctx.strokeRect(cropStartX, cropStartY, cropEndX - cropStartX, cropEndY - cropStartY);
}

function startAction(e) {
    const rect = canvas.getBoundingClientRect();
    var pos = getMousePos(canvas, e);
    const mouseX = pos.x;
    const mouseY = pos.y;

    const minX = Math.min(cropStartX, cropEndX);
    const maxX = Math.max(cropStartX, cropEndX);
    const minY = Math.min(cropStartY, cropEndY);
    const maxY = Math.max(cropStartY, cropEndY);

    if (mouseX > minX && mouseX < maxX && mouseY > minY && mouseY < maxY) {
        conditionalSwap();
        isMovingCrop = true;
        cropOffsetX = mouseX - cropStartX;
        cropOffsetY = mouseY - cropStartY;
    } else {
        // Start a new crop area
        cropOldX = mouseX;
        cropOldY = mouseY;
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
function cropExpectedWidth() {
    return cropHeight() * aspectRatio;
}
function cropExpectedHeight() {
    return cropWidth() / aspectRatio;
}

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

function duringAction(e) {
    const canvas = document.getElementById('imageCanvas');
    const rect = canvas.getBoundingClientRect();
    var pos = getMousePos(canvas, e);
    const mouseX = pos.x;
    const mouseY = pos.y;

    if (isDragging) {
        handleDragging(mouseX, mouseY);
    } else if (isMovingCrop) {
        handleMoving(mouseX, mouseY);
    }

    // Redraw the canvas, and the crop area after any movement
    resetCanvas();
}

function handleDragging(mouseX, mouseY) {
    if (startedDragging === false) {
        cropStartX = cropOldX;
        cropStartY = cropOldY;
        startedDragging = true;
    }
    createSquare(mouseX, mouseY);
}

function handleMoving(mouseX, mouseY) {
    // Move the existing crop area
    const newCropStartX = mouseX - cropOffsetX;
    const newCropStartY = mouseY - cropOffsetY;

    // Calculate the new crop end coordinates while keeping the 16:9 aspect ratio
    const cropWidth = cropEndX - cropStartX;
    const cropHeight = cropEndY - cropStartY;
    cropEndX = newCropStartX + cropWidth;
    cropEndY = newCropStartY + cropHeight;

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
function createSquare(newX, newY) {
    const cropWidth = newX - cropStartX;
    let cropHeight = Math.abs(cropWidth / aspectRatio);
    if (newY < cropStartY) cropHeight *= -1;

    cropEndX = cropStartX + cropWidth;
    cropEndY = cropStartY + cropHeight;

    shrinkToBorders();
}

function endAction() {
    if (startedDragging === false && isDragging === true) {
        createSquare(cropOldX, cropOldY)
    }
    isDragging = false;
    startedDragging = false;
    isMovingCrop = false;


    // conditionalSwap();
    // Redraw the crop area after the action ends
    resetCanvas();
}

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
    // Hide the popup and show the cropped image
    // document.getElementById('imagePopup').style.display = 'none';

    // Set the cropped image as a hidden input to send it to the server
    document.getElementById('croppedImageInput').value = croppedImageDataUrl;
}
