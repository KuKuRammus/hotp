import jsQR from "jsqr";
import crypto from 'crypto'

const GENERATORS_STORE_KEY = 'generators';
const TIME_FRAME_DURATION = 30;
const CODE_GENERATION_INTERVAL = 1;
const GENERATED_CODE_LENGTH = 5;
const HMAC_ALG = 'sha1';

const elements = {
    initScanControl: null,
    shutdownScanControl: null,
    cameraFeedWrapper: null,
    cameraFeed: null,
    cameraFeedContext: null,
    generatorList: null,
    video: null,
    videoSizeElement: 0,
    videoStream: null
};

let generators = [];


function persistToLocalStorage() {
    localStorage.setItem(GENERATORS_STORE_KEY, JSON.stringify(generators));
}

function loadFromLocalStorage() {
    try {
        const loaded = JSON.parse(localStorage.getItem(GENERATORS_STORE_KEY));
        if (loaded === null) {
            return [];
        }
        return loaded;
    } catch (e) {
        return [];
    }
}

function getGeneratorIndexByNameOrNull(name) {
    const index = generators.findIndex(generator => generator.name === name);
    return index === -1 ? null : index;
}

function addGenerator(name, secret) {
    const generatorIndex = getGeneratorIndexByNameOrNull(name);
    if (generatorIndex !== null) {
        return null;
    }

    generators.push({ name, secret });
    persistToLocalStorage();

    return generators.length;
}

function updateGeneratorElementData(element, code, timeTillNextFrame) {
    element.querySelector('.otp').innerText = code.toUpperCase();
    element.querySelector('.timeTillNextCode').innerText = `${timeTillNextFrame}s`
}

function destroyGeneratorElement(name) {
    // Remove from DOM
    const element = document.getElementById(name);
    if (element) {
        element.remove();
    }

    // Remove from internal storage
    removeGenerator(name);
}

function spawnGeneratorElement(name) {
    // Create dom element
    const element = document.createElement('div');
    element.id = name;
    element.className = 'tile mb-1 tile-bg-gray';
    element.innerHTML = `
        <p class="code">${name}</p>
        <p>
            <b class="code otp"></b>
            <span style="opacity: 0.3">Next code in: <span class="timeTillNextCode">0</span></span>
        </p>
        <div><h3 class="otp" style="font-family: monospace"></h3></div>
        <a href="#" class="remove-control danger">Remove</a>
    `;

    // Add ability to remove element
    element.querySelector('.remove-control').addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        destroyGeneratorElement(name);
    });

    elements.generatorList.insertBefore(element, elements.generatorList.firstChild);
}

function removeGenerator(name) {
    const generatorIndex = getGeneratorIndexByNameOrNull(name);
    if (generatorIndex === null) {
        return;
    }

    generators.splice(generatorIndex, 1);
    persistToLocalStorage();
}

function handleValidQrDetected(name, secret) {
    const nextGeneratorIndex = addGenerator(name, secret);
    if (nextGeneratorIndex === null) {
        // Generator already exists
        return;
    }

    spawnGeneratorElement(name);
}

function shutdownScanner() {
    elements.video.pause();
    elements.video.srcObject.getTracks()[0].stop();
    elements.video.removeAttribute('src');
    elements.video.load();

    // Switch controls
    elements.initScanControl.style.display = 'block';
    elements.shutdownScanControl.style.display = 'none';
    elements.cameraFeedWrapper.style.display = 'none';
}

function processCameraFrame() {
    if (elements.video.readyState === elements.video.HAVE_ENOUGH_DATA) {
        elements.cameraFeedContext.drawImage(
            elements.video,
            0, 0,
            elements.cameraFeed.width, elements.cameraFeed.height,
            0, 0,
            elements.cameraFeed.width, elements.cameraFeed.height,
        );
        const imageData = elements.cameraFeedContext.getImageData(
            0, 0,
            elements.cameraFeed.width, elements.cameraFeed.height
        );
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: "dontInvert"
        });

        if (code) {
            try {
                const decoded = JSON.parse(code.data);
                if (!('name' in decoded) || !('secret' in decoded)) {
                    throw 'Invalid QR code';
                }
                handleValidQrDetected(decoded['name'], decoded['secret']);
                return shutdownScanner();
            } catch (e) {
                alert('Invalid QR code');
            }
        }
    }

    requestAnimationFrame(processCameraFrame);
}

function initScanner() {
    // Switch controls
    elements.initScanControl.style.display = 'none';
    elements.shutdownScanControl.style.display = 'block';
    elements.cameraFeedWrapper.style.display = 'block';

    elements.cameraFeed.width = elements.videoSizeElement.clientWidth;
    elements.cameraFeed.height = elements.videoSizeElement.clientWidth;

    // Start receiving frames
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then((stream) => {
            elements.video.srcObject = stream;
            elements.video.setAttribute("playsinline", true);
            elements.video.setAttribute("muted", true);
            elements.video.play();
            requestAnimationFrame(processCameraFrame);
        })
}

function startCodeGeneration() {
    setInterval(() => {
        const now = Math.floor((new Date().getTime()) / 1000);
        const timeFrameNumber = Math.floor(now / TIME_FRAME_DURATION);
        const timeTillNextFrame = TIME_FRAME_DURATION - (now - (timeFrameNumber * TIME_FRAME_DURATION));

        generators.forEach((generator) => {
            const domElement = document.getElementById(generator.name);
            if (!domElement) return null;

            // Generate code
            const hmac = crypto.createHmac(HMAC_ALG, generator.secret);
            hmac.update(timeFrameNumber.toString());
            const code = hmac.digest('hex').substr(0, GENERATED_CODE_LENGTH);

            updateGeneratorElementData(domElement, code, timeTillNextFrame);
        });
    }, CODE_GENERATION_INTERVAL * 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    // Fetch all DOM elements
    elements.initScanControl = document.getElementById('init-scan-control');
    elements.shutdownScanControl = document.getElementById('shutdown-scan-control');
    elements.cameraFeedWrapper = document.getElementById('camera-feed-wrapper');
    elements.cameraFeed = document.getElementById('camera-feed');
    elements.cameraFeedContext = elements.cameraFeed.getContext('2d');
    elements.generatorList = document.getElementById('generator-list');
    elements.video = document.createElement('video');
    elements.videoSizeElement = document.getElementById('camera-view-width');

    // Set initial DOM elements state
    elements.initScanControl.style.display = 'block';
    elements.shutdownScanControl.style.display = 'none';
    elements.cameraFeedWrapper.style.display = 'none';

    // Add event listener
    elements.initScanControl.addEventListener('click', initScanner.bind(this));
    elements.shutdownScanControl.addEventListener('click', shutdownScanner.bind(this));

    // Load generators from local storage
    generators = loadFromLocalStorage();
    generators.forEach((generator) => {
        spawnGeneratorElement(generator.name);
    });


    startCodeGeneration();
});
