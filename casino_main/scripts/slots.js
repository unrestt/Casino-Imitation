// Pobieranie elementów z DOM
const slots = document.querySelectorAll('.slot');
const start = document.querySelector('#start');
const resultDisplay = document.querySelector('#result');
const bet = document.querySelector('#bet');

let tab = [[], [], []];

const img = [
    '<div class="single-slot"><img src="images/slots/arbuz.png"></div>',
    '<div class="single-slot"><img src="images/slots/cytryna.png"></div>',
    '<div class="single-slot"><img src="images/slots/pomarancza.png"></div>',
    '<div class="single-slot"><img src="images/slots/truskawka.png"></div>',
    '<div class="single-slot"><img src="images/slots/winogrono.png"></div>',
    '<div class="single-slot"><img src="images/slots/wisnie.png"></div>',
    '<div class="single-slot"><img src="images/slots/koniczynka.png"></div>',
    '<div class="single-slot"><img src="images/slots/diament.png"></div>'
];
const halveBetButton = document.querySelector('#halveBet');
const doubleBetButton = document.querySelector('#doubleBet');
const betInput = document.querySelector('#bet');

halveBetButton.addEventListener('click', function () {
    let betValue = parseFloat(betInput.value);
    if (betValue > 1) {
        betInput.value = (betValue / 2).toFixed(2);
    }
});

doubleBetButton.addEventListener('click', function () {
    let betValue = parseFloat(betInput.value);
    betInput.value = (betValue * 2).toFixed(2);
});

function createSlots() {
    for (let i = 0; i < slots.length; i++) {
        slots[i].innerHTML = '';
        for (let j = 0; j < 50; j++) {
            const randomIndex = Math.floor(Math.random() * img.length);
            slots[i].innerHTML += img[randomIndex];
        }
    }
}

let freeTries = 0;
function checkSlots(tab) {
    let score = 0;
    freeTries--;

    let winningRows = [];
    let winningColumns = [];
    let diagonals = [];

    document.querySelectorAll('.single-slot').forEach(div => div.classList.remove('winning'));

    for (let i = 0; i < 3; i++) {
        if (tab[i][0] === tab[i][1] && tab[i][1] === tab[i][2]) {
            winningRows.push(i);
        }
    }
    for (let i = 0; i < 3; i++) {
        if (tab[0][i] === tab[1][i] && tab[1][i] === tab[2][i]) {
            winningColumns.push(i);
        }
    }
    if (tab[0][0] === tab[1][1] && tab[1][1] === tab[2][2]) {
        diagonals.push("left");
    }
    if (tab[0][2] === tab[1][1] && tab[1][1] === tab[2][0]) {
        diagonals.push("right");
    }

    const winningLines = [...winningRows, ...winningColumns, ...diagonals];
    winningLines.forEach((line) => {
        let symbol;
        let divsToHighlight = [];

        if (typeof line === "number") {
            if (winningRows.includes(line)) {
                divsToHighlight = slots[line].querySelectorAll('.single-slot:nth-child(-n+3)');
                symbol = tab[line][0];
            } else if (winningColumns.includes(line)) {
                for (let i = 0; i < 3; i++) {
                    divsToHighlight.push(slots[i].querySelector(`.single-slot:nth-child(${line + 1})`));
                }
                symbol = tab[0][line];
            }
        } else if (line === "left") {
            divsToHighlight = [
                slots[0].querySelector('.single-slot:nth-child(1)'),
                slots[1].querySelector('.single-slot:nth-child(2)'),
                slots[2].querySelector('.single-slot:nth-child(3)')
            ];
            symbol = tab[0][0];
        } else if (line === "right") {
            divsToHighlight = [
                slots[0].querySelector('.single-slot:nth-child(3)'),
                slots[1].querySelector('.single-slot:nth-child(2)'),
                slots[2].querySelector('.single-slot:nth-child(1)')
            ];
            symbol = tab[0][2];
        }

        divsToHighlight.forEach(div => div?.classList.add('winning'));

        if (symbol.includes('diament')) {
            score += 5;
        } else if (symbol.includes('koniczynka')) {
            freeTries += 5;
        } else {
            score += 1;
        }
    });

    const result = score * bet.value * 2;
    let resultMessage = `-${bet.value}$`;
    if (result > 0)
        resultMessage = `${result}$`;
    if (freeTries > 0) {
        resultMessage += ` Masz ${freeTries} dodatkowych prób!`;
    }
    resultDisplay.textContent = resultMessage;
}
const inputContainer = document.querySelector('.input-container');
start.addEventListener('click', () => {
    start.disabled = true;
    start.textContent = '';
    const spinner = document.createElement('div');
    spinner.classList.add('loading-spinner');
    start.appendChild(spinner);
    inputContainer.classList.add('dimmed');

    slots.forEach(slot => {
        slot.innerHTML = '';
    });

    tab = [[], [], []];

    for (let i = 0; i < slots.length; i++) {
        for (let j = 0; j < 50; j++) {
            const randomIndex = Math.floor(Math.random() * img.length);
            slots[i].innerHTML += img[randomIndex];
        }
    }

    let animationsEnded = 0;

    slots.forEach(slot => {
        slot.classList.remove('animate');
        void slot.offsetWidth;
        slot.classList.add('animate');

        slot.addEventListener('animationend', function onAnimationEnd() {
            animationsEnded++;
            if (animationsEnded === slots.length) {
                slots.forEach((slot, index) => {
                    const visibleDivs = slot.querySelectorAll('.single-slot');
                    const firstThreeDivs = Array.from(visibleDivs).slice(0, 3);

                    firstThreeDivs.forEach(div => {
                        const img = div.querySelector('img');
                        tab[index].push(img.src);
                    });
                });

                checkSlots(tab);

                start.disabled = false;
                start.textContent = 'Start';
                inputContainer.classList.remove('dimmed');
            }
            slot.removeEventListener('animationend', onAnimationEnd);
        });
    });
});



createSlots();