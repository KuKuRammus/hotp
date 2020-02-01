import QrScanner from "qr-scanner";
import crypto from 'crypto'
QrScanner.WORKER_PATH = '/build/vendor/scanner/qr-scanner-worker.min.js';

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
    generatorList: null
};

let generators = [];

let qrScanner = null;


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
    element.querySelector('.code').innerText = code.toUpperCase();
    element.querySelector('.timeTillNextCode').innerText = `${timeTillNextFrame} s.`
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
    element.innerHTML = `
        <div><b>${name}</b></div>
        <div><h3 class="code" style="font-family: monospace"></h3></div>
        <div>Next code in: <span class="timeTillNextCode">0</span></div>
        <div><button class="remove-control">Remove</button></div>
        <hr />
    `;

    // Add ability to remove element
    element.querySelector('.remove-control').addEventListener('click', () => {
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
    if (qrScanner === null) {
        return;
    }

    // Switch controls
    elements.initScanControl.style.display = 'block';
    elements.shutdownScanControl.style.display = 'none';
    elements.cameraFeedWrapper.style.display = 'none';

    qrScanner.stop();
    qrScanner.destroy();
}

function initScanner() {
    if (qrScanner !== null) {
        shutdownScanner();
    }

    // Switch controls
    elements.initScanControl.style.display = 'none';
    elements.shutdownScanControl.style.display = 'block';
    elements.cameraFeedWrapper.style.display = 'block';

    // Start scanner
    qrScanner = new QrScanner(elements.cameraFeed, (result) => {
        try {
            const decoded = JSON.parse(result);
            if (!('name' in decoded) || !('secret' in decoded)) {
                throw 'Invalid QR code';
            }
            handleValidQrDetected(decoded['name'], decoded['secret']);
            shutdownScanner();
        } catch (e) {
            alert('Invalid QR code');
        }
    });
    qrScanner.start();

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
    elements.generatorList = document.getElementById('generator-list');

    // Set initial DOM elements state
    elements.initScanControl.style.display = 'block';
    elements.shutdownScanControl.style.display = 'none';
    elements.cameraFeedWrapper.style.display = 'none';

    // Add event listener
    elements.initScanControl.addEventListener('click', initScanner.bind(this));
    elements.shutdownScanControl.addEventListener('click', shutdownScanner.bind(this));

    // Load generators from local storage
    generators = loadFromLocalStorage();
    console.log(generators);
    generators.forEach((generator) => {
        spawnGeneratorElement(generator.name);
    });


    startCodeGeneration();
});
