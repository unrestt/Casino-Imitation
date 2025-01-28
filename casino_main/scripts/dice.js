document.addEventListener('DOMContentLoaded', function() {
    const reverse = document.querySelector("#reverse");
    const reverseText = document.getElementById('reverse-text');
    const aboveInput = document.getElementById('above'); 
    let reverseValue = true;
    const result = document.querySelector('#result');
    const chance = document.querySelector('#chance');
    const inputRange = document.getElementById('range');
    const mnoznik = document.getElementById('mnoznik');
    const bet = document.querySelector('#bet');
    const coinsResult = document.querySelector('#coinsResult');
    let valueBar;
    let results = [];


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


    function addResult(number, isWin) {
        results.unshift({ number, isWin });

        if (results.length > 5) {
            results.pop();
        }

        displayResults();
    }

    function displayResults() {
        const resultsContainer = document.getElementById('results-container');
        const resultDiv = document.createElement('div');
        resultDiv.classList.add('result');
        
        if (results[0].isWin) {
            resultDiv.classList.add('green'); 
        } else {
            resultDiv.classList.add('gray'); 
        }
        resultDiv.textContent = results[0].number;
        resultsContainer.prepend(resultDiv);
        
        setTimeout(() => {
            resultDiv.classList.add('visible');
        }, 10);
    
        const currentResults = Array.from(resultsContainer.children);
        currentResults.forEach((result, index) => {
            if (index !== 0) { 
                result.classList.add('slide-left');
            }
        });
    
        setTimeout(() => {
            currentResults.forEach(result => {
                result.classList.remove('slide-left');
            });
        }, 500);

        if (currentResults.length > 5) {
            resultsContainer.removeChild(currentResults[currentResults.length - 1]);
        }
    }
      
    function changeColor(){
        valueBar = inputRange.value;
        aboveInput.value = inputRange.value;
        const percentage = (valueBar - inputRange.min) / (inputRange.max - inputRange.min) * 100;
        if(reverseValue){
            reverseText.innerHTML = 'Poniżej';
            inputRange.style.background = `linear-gradient(to right, #4CAF50 ${percentage}%, #f44336 ${percentage}%)`;
            chance.value = Math.floor(valueBar);
        }else{
            reverseText.innerHTML = 'Powyżej';
            inputRange.style.background = `linear-gradient(to right, #f44336 ${percentage}%, #4CAF50 ${percentage}%)`;
            chance.value = 100 - Math.floor(valueBar);
        }
        mnoznik.value = (100/chance.value - 0.0102).toFixed(4);
    }

    inputRange.addEventListener('input', changeColor);

    reverse.addEventListener('click', () => {
        reverseValue = !reverseValue;
        inputRange.value = 100 - inputRange.value;
        changeColor();
    });

    let previous = 0;
    const img = document.createElement("img");
    img.setAttribute("src", "images/cube.png");
    const p = document.createElement("p");
    result.appendChild(img);
    result.appendChild(p);

    const startButton = document.getElementById('start');
    startButton.addEventListener('click', () => {
        result.style.display = "flex";
        const number = Math.floor(Math.random() * 100);
        p.textContent = number;
    
        const inputContainer = document.querySelector('.input-container');
        const bottomInputContainer = document.querySelectorAll('.bottom-input-container');
    
        inputContainer.classList.add('dimmed');
        bottomInputContainer.forEach(panel => panel.classList.add('dimmed'));
    
        const spinner = document.createElement('div');
        spinner.classList.add('loading-spinner');
        startButton.textContent = '';
        startButton.appendChild(spinner);
        startButton.disabled = true; 
    
        const animation = result.animate(
            [
                { left: `${previous}%` },
                { left: `${number}%` },
            ],
            {
                duration: 400,
                iterations: 1,
                fill: 'forwards'
            }
        );
    
        animation.onfinish = () => {
            inputContainer.classList.remove('dimmed');
            bottomInputContainer.forEach(panel => panel.classList.remove('dimmed'));
            spinner.remove();
            startButton.disabled = false;
            startButton.textContent = 'Postaw';
        };
    
        previous = number;

        
        const isWin = (reverseValue && number < inputRange.value) || (!reverseValue && number > inputRange.value);
        addResult(number, isWin);

        
        if (isWin) {
            p.style.color = '#4CAF50';
        } else {
            p.style.color = '#f44336';
        }
    
        if ((inputRange.value >= number && reverseValue) || (inputRange.value <= number && !reverseValue)) {
            const winCoins = (bet.value * mnoznik.value - bet.value).toFixed(2);
            coinsResult.innerHTML = `+${winCoins}`;
        } else {
            coinsResult.innerHTML = `-${bet.value}`;
        }
    });

    chance.addEventListener('change', () => {
        if (reverseValue)
            inputRange.value = chance.value;
        else
            inputRange.value = 100 - chance.value;
        changeColor();
    });

    mnoznik.addEventListener('change', () => {
        const newChance = (100 / mnoznik.value) + 0.0102;
        if (newChance >= inputRange.min && newChance <= inputRange.max) {
            if (reverseValue) {
                chance.value = Math.floor(newChance);
                inputRange.value = chance.value;
            } else {
                chance.value = Math.floor(100 - newChance);
                inputRange.value = 100 - chance.value;
            }
            changeColor();
        } else {
            alert("Podany mnożnik spowodował wartość poza zakresem! Sprawdź swoje dane.");
        }
    });
});
