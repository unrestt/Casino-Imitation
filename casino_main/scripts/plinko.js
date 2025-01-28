const slotMultipliers = [10, 5, 2, 1, 0.7, 0.5, 0.7, 1, 2, 5, 10];

function startGame() {
    const { Engine, Render, Runner, Bodies, Composite, Events } = Matter;

    const rows = 10;
    const engine = Engine.create();
    const world = engine.world;

    engine.gravity.x = 0;
    engine.gravity.y = 1;

    const gameContainer = document.getElementById('game-container');
    const render = Render.create({
        element: gameContainer,
        engine: engine,
        options: {
            width: 1000,
            height: 700,
            wireframes: false,
            background: '#0D1A24',
        }
    });
    Render.run(render);

    const runner = Runner.create();
    Runner.run(runner, engine);

    // const ground = Bodies.rectangle(500, 700, 670, 20, { isStatic: true });
    // Composite.add(world, ground);

    const pegs = [];
    const pegRadius = 10;
    const spacing = 60;
    const pyramidOffsetY = 150 - 100;

    for (let row = 0; row < rows; row++) {
        const numPegsInRow = row + 3;
        const yPosition = pyramidOffsetY + row * spacing;
        for (let col = 0; col < numPegsInRow; col++) {
            const xPosition = (1000 / 2) + (col - (numPegsInRow - 1) / 2) * spacing;
            const peg = Bodies.circle(xPosition, yPosition, pegRadius, { isStatic: true, render: { fillStyle: '#eee' }, label: 'Peg' });
            pegs.push(peg);
        }
    }
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
    Composite.add(world, pegs);

    const slots = [];
    const slotElements = [];
    const slotWidth = 60;
    const slotOffsetY = 750 - 100;
    const numSlots = rows + 2;

    const startX = (1000 - (numSlots * slotWidth)) / 2 + 30;

    const slotsContainer = document.createElement('div');
    slotsContainer.id = 'slots';
    gameContainer.appendChild(slotsContainer);

    for (let i = 0; i < numSlots - 1; i++) {
        const x = startX + i * slotWidth + slotWidth / 2;

        const slotDiv = document.createElement('div');
        slotDiv.classList.add('slot');
        slotDiv.classList.add(`slot-${i}`); // Dodanie unikalnej klasy dla każdego slotu
        slotDiv.style.backgroundColor = '#FF0000'; // Ustawienie domyślnego koloru (możesz zmienić na inny)

        const multiplierText = document.createElement('span');
        multiplierText.innerText = slotMultipliers[i] + "x";
        multiplierText.classList.add('multiplier-text');

        slotDiv.appendChild(multiplierText);
        slotsContainer.appendChild(slotDiv);
        slotElements.push(slotDiv);

        const slot = Bodies.rectangle(x, slotOffsetY + 30, slotWidth - 10, 100, {
            isStatic: true,
            collisionFilter: {
                category: 0x0002,
                mask: 0xFFFF
            },
            render: {
                visible: false
            },
            label: `Slot-${i}`
        });

        slots.push(slot);
    }
    Composite.add(world, slots);

    const slotCounts = new Array(slots.length).fill(0);

    Events.on(engine, 'collisionStart', (event) => {
        event.pairs.forEach((pair) => {
            const { bodyA, bodyB } = pair;

            const ball = bodyA.label === 'Ball' ? bodyA : bodyB.label === 'Ball' ? bodyB : null;
            const peg = bodyA.label === 'Peg' ? bodyA : bodyB.label === 'Peg' ? bodyB : null;
            const slotIndex = slots.findIndex(slot => slot.id === (bodyA.id || bodyB.id));

            if (ball && peg) {
                // Tworzenie efektu rozbłysku na pegu
                const flashEffect = document.createElement('div');
                flashEffect.classList.add('flash');
                flashEffect.style.left = `${peg.position.x - 20}px`; // Ustawienie pozycji efektu na peg
                flashEffect.style.top = `${peg.position.y - 20}px`; // Ustawienie pozycji efektu na peg
                gameContainer.appendChild(flashEffect);

                // Usunięcie efektu po zakończeniu animacji
                setTimeout(() => {
                    flashEffect.remove();
                }, 300); // Czas trwania animacji
            }

            if (ball && slotIndex >= 0) {
                const slotDiv = slotElements[slotIndex];
                slotDiv.classList.add('slot-hit');

                // Zwiększ liczbę piłek w danym slocie
                slotCounts[slotIndex]++;

                // Usuwanie piłki po trafieniu do slotu
                Composite.remove(world, ball);

                setTimeout(() => {
                    slotDiv.classList.remove('slot-hit');
                }, 300);
            }
        });
    });
    const start = document.querySelector('#start');
    start.addEventListener('click', () => {
        const randomDirectionX = Math.random() < 0.9 ? 0 : (Math.random() < 0.5 ? -0.0000005 : 0.0000005);
        const randomDirectionY = Math.random() * 0.0001;
    
        const ball = Bodies.circle(500, 0, 10, {
            restitution: 0.5,
            collisionFilter: {
                category: 0x0001,
                mask: 0xFFFF
            },
            render: { fillStyle: '#FEF04A' },
            label: 'Ball'
        });
    
        Matter.Body.applyForce(ball, { x: ball.position.x, y: ball.position.y }, { x: randomDirectionX, y: randomDirectionY });
    
        Composite.add(world, ball);
    });

    setInterval(() => {

        const section = document.querySelector('section#score');
        section.innerHTML = "Wyniki:<br>";

        slotCounts.forEach((count, index) => {
            if (count > 0) {
                const totalValue = count * slotMultipliers[index];
                const slotColor = slots[index].render.fillStyle || '#FF0000';
                section.innerHTML += `<p style="background-color: ${slotColor};">Slot ${index + 1}: ${count} piłek, Mnożnik: ${slotMultipliers[index]}x, Łączny wynik: ${totalValue}</p>`;
            }
        });
    }, 500);
}

startGame();
