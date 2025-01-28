function generateBombs(size, bombCount) {
    const bombs = Array(size).fill(false);
    let count = 0;

    while (count < bombCount) {
        const index = Math.floor(Math.random() * size);
        if (!bombs[index]) {
            bombs[index] = true;
            count++;
        }
    }

    return bombs;
}

function printSquares() {
    squares.innerHTML = '';
    for (let i = 0; i < 25; i++) {
        const div = document.createElement('div');
        div.classList.add('square');
        div.setAttribute('data-index', i);
        squares.appendChild(div);
    }
}

function calculateMultiplier(uncovered, safeFields) {
    const K = 3.91;
    return 1 + (uncovered / safeFields) * K;
}

const numberOfBombs = document.querySelector('#numberOfBombs');
const bet = document.querySelector('#bet');
const start = document.querySelector('#start');
const end = document.querySelector('#end');
const squares = document.querySelector('.squares');
const mnoznik = document.querySelector('.mnoznik');
const mnoznik2 = document.querySelector('#mnoznik-2');

let bombs = [];
let uncoveredSquares = 0;
let gameOver = false;

function selectSquare(square) {
    square.classList.add('selected');
}

function dimUnselectedSquares(squares) {
    squares.forEach((square) => {
        if (!square.classList.contains('selected') && !square.classList.contains('dim')) {
            square.classList.add('dim');
        }
    });
}

function revealAllSquares(squares) {
    bombs.forEach((isBomb, i) => {
        const square = squares[i];
        if (!square.classList.contains('faceUpSafe') && !square.classList.contains('faceUpBomb')) {
            square.classList.add(isBomb ? 'faceUpBomb' : 'faceUpSafe');
            const img = document.createElement('img');
            img.src = isBomb ? 'images/bombs/bomb.png' : 'images/bombs/diamond.png';
            img.alt = '';
            square.appendChild(img);
        }
    });
}

const halveBetButton = document.querySelector('#halveBet');
const doubleBetButton = document.querySelector('#doubleBet');
const betInput = document.querySelector('#bet');
const darkInput = document.getElementById("darkInput");
const betValueText = document.getElementById("bet-value");
const actualWin = document.querySelector('#actualWin');

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

const winPopup = document.querySelector('.win-popup');

start.addEventListener('click', function () {

    winPopup.style.display = 'none';
    const bombCount = parseInt(numberOfBombs.value, 10);
    const betValue = parseFloat(bet.value);
    actualWin.textContent = betValue + " USD";
    darkInput.value = betValue;
    betValueText.innerHTML = betValue + " USD";

    if (bombCount > 0 && bombCount < 25 && betValue > 0) {
        bombs = generateBombs(25, bombCount);
        printSquares();
        uncoveredSquares = 0;
        mnoznik.innerHTML = "Całkowity zysk (1.00x)";
        mnoznik2.innerHTML = "1.00x";
        gameOver = false;

        const bombCountElement = document.querySelector('#bombCount');
        bombCountElement.textContent = bombCount;

        const diamondCountElement = document.querySelector('#diamondCount');
        const diamondCount = 25 - bombCount;
        diamondCountElement.textContent = diamondCount;

        const prePanel = document.querySelector('.bombs-left-panel-pre');
        const gamePanel = document.querySelector('.bombs-left-panel-game');

        if (prePanel && gamePanel) {
            prePanel.style.display = 'none';
            gamePanel.style.display = 'flex';
        }

        bet.disabled = true;

        const safeSquares = 25 - bombCount;

        const squares = document.querySelectorAll('.square');
        squares.forEach(function (square) {
            square.addEventListener('click', function () {
                if (gameOver || square.classList.contains('faceUpSafe') || square.classList.contains('faceUpBomb')) return;

                selectSquare(square);

                const index = square.getAttribute('data-index');
                if (bombs[index]) {
                    dimUnselectedSquares(squares);
                    revealAllSquares(squares);
                    gameOver = true;
                    bet.disabled = false;

                    if (prePanel && gamePanel) {
                        gamePanel.style.display = 'none';
                        prePanel.style.display = 'flex';
                    }
                } else {
                    square.classList.add('faceUpSafe');
                    const img = document.createElement('img');
                    img.src = 'images/bombs/diamond.png';
                    img.alt = '';
                    square.appendChild(img);

                    uncoveredSquares++;
                    const multiplier = calculateMultiplier(uncoveredSquares, safeSquares);
                    mnoznik.innerHTML = "Całkowity zysk (" + multiplier.toFixed(2) + ")";
                    mnoznik2.innerHTML = multiplier.toFixed(2);

                    // Aktualizuj wartość wygranej dynamicznie
                    const winAmount = betValue * multiplier;
                    actualWin.textContent = winAmount.toFixed(2) + " USD";

                    const remainingDiamonds = diamondCount - uncoveredSquares;
                    diamondCountElement.textContent = remainingDiamonds;

                }
            });
        });

        end.disabled = false;
        end.addEventListener('click', function () {
            dimUnselectedSquares(squares);
            revealAllSquares(squares);

            const multiplier = calculateMultiplier(uncoveredSquares, safeSquares);
            const winAmount = betValue * multiplier;
            const winText = document.querySelector("#win-text");
            winText.innerHTML = winAmount.toFixed(2) + " USD";

            mnoznik.innerHTML = "Całkowity zysk (" + multiplier.toFixed(2) + 'x)';
            mnoznik2.innerHTML = multiplier.toFixed(2) + 'x';

            winPopup.style.display = 'flex';

            if (prePanel && gamePanel) {
                gamePanel.style.display = 'none';
                prePanel.style.display = 'flex';
            }

            end.disabled = true;
            bet.disabled = false;
        });
    } else {
        alert('Podaj liczbę bomb w zakresie od 1 do 24 i postaw zakład.');
    }
});
