let bot1 = [];
let bot2 = [];
let bot3 = [];
let bot4 = [];
let player = [];
let table = [];

let bot1Coins = 1000;
let bot2Coins = 1000;
let bot3Coins = 1000;
let bot4Coins = 1000;
let playerCoins = 1000;

let currentBet = 20;


const bot1Section = document.querySelector('section#bot1');
const bet = document.querySelector('section#bet');
const bot2Section = document.querySelector('section#bot2');
const bot3Section = document.querySelector('section#bot3');
const bot4Section = document.querySelector('section#bot4');
const playerSection = document.querySelector('section#player');
const tableSection = document.querySelector('section#table');

const play = document.querySelector('#play');
const fold = document.querySelector('#fold');
const playValue = document.querySelector('#playValue');

let bot1Score = 0;
let bot2Score = 0;
let bot3Score = 0;
let bot4Score = 0;
let playerScore = 0;

let cards = [];

const suits = ["♥", "♦", "♠", "♣"];
const ranks = ["2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K", "A"];

for(const suit of suits){
    for(const rank of ranks){
        cards.push({ rank, suit });
    }
}

function shuffle(deck) {
    for (let i = deck.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [deck[i], deck[j]] = [deck[j], deck[i]];
    }
}

function dealingCards() {
    if (cards.length < 10) {
        console.error("Not enough cards to deal!");
        return;
    }

    for (let i = 0; i < 2; i++) {
        bot1.push(cards[0]);
        bot2.push(cards[1]);
        bot3.push(cards[2]);
        bot4.push(cards[3]);
        player.push(cards[4]);

        cards.splice(0, 5);

        bot1Section.innerHTML += ` ${bot1[bot1.length - 1].rank}${bot1[bot1.length - 1].suit} `;
        bot2Section.innerHTML += ` ${bot2[bot2.length - 1].rank}${bot2[bot2.length - 1].suit} `;
        bot3Section.innerHTML += ` ${bot3[bot3.length - 1].rank}${bot3[bot3.length - 1].suit} `;
        bot4Section.innerHTML += ` ${bot4[bot4.length - 1].rank}${bot4[bot4.length - 1].suit} `;
        playerSection.innerHTML += ` ${player[player.length - 1].rank}${player[player.length - 1].suit} `;
    }
}

function tour() {
    table.push(cards[0]);
    cards.splice(0, 1);
    tableSection.innerHTML += ` ${table[table.length - 1].rank}${table[table.length - 1].suit} `;
}

function isStraight(allCardsRank) {
    const rankOrder = ["2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K", "A"];
    const ranksSet = new Set(allCardsRank);
    if (ranksSet.size !== 5) return false;
    const ranksArray = Array.from(ranksSet).sort((a, b) => rankOrder.indexOf(a) - rankOrder.indexOf(b));
    return rankOrder.indexOf(ranksArray[0]) + 4 === rankOrder.indexOf(ranksArray[4]);
}

function countRankOccurrences(cards) {
    return cards.reduce((acc, card) => {
        acc[card.rank] = (acc[card.rank] || 0) + 1;
        return acc;
    }, {});
}

function checkValue(cards, table) {
    const allCards = cards.concat(table);
    const allCardsRank = allCards.map(card => card.rank);
    const allCardsSuit = allCards.map(card => card.suit);

    const rankCounts = countRankOccurrences(allCards);
    const isFlush = allCardsSuit.filter(suit => suit === allCardsSuit[0]).length >= 5;
    const isRoyalFlush = isFlush && ["10", "J", "Q", "K", "A"].every(rank => allCardsRank.includes(rank));
    const isStraightFlush = isFlush && isStraight(allCardsRank);

    const getCardValue = (rank) => ranks.indexOf(rank);

    const highestRank = Math.max(...allCardsRank.map(getCardValue));
    const rankValues = Object.entries(rankCounts).map(([rank, count]) => ({
        rank,
        value: getCardValue(rank),
        count,
    }));
    rankValues.sort((a, b) => b.value - a.value);

    if (isRoyalFlush) {
        return 117 + highestRank;
    }

    if (isStraightFlush) {
        return 104 + highestRank;
    }

    if (Object.values(rankCounts).includes(4)) {
        const fourOfAKindValue = rankValues.find(r => r.count === 4).value;
        return 91 + fourOfAKindValue;
    }

    if (Object.values(rankCounts).includes(3) && Object.values(rankCounts).filter(c => c >= 2).length >= 2) {
        const threeOfAKindValue = rankValues.find(r => r.count === 3).value;
        return 78 + threeOfAKindValue;
    }

    if (isFlush) {
        return 65 + highestRank;
    }

    if (isStraight(allCardsRank)) {
        return 52 + highestRank;
    }

    if (Object.values(rankCounts).includes(3)) {
        const threeOfAKindValue = rankValues.find(r => r.count === 3).value;
        return 39 + threeOfAKindValue;
    }

    if (Object.values(rankCounts).filter(count => count === 2).length === 2) {
        const pairValues = rankValues.filter(r => r.count === 2).map(r => r.value);
        const highestPair = Math.max(...pairValues);
        return 26 + highestPair;
    }

    if (Object.values(rankCounts).includes(2)) {
        const pairValue = rankValues.find(r => r.count === 2).value;
        return 13 + pairValue;
    }

    return 0 + highestRank;
}

function countRankOccurrences(cards) {
    return cards.reduce((acc, card) => {
        acc[card.rank] = (acc[card.rank] || 0) + 1;
        return acc;
    }, {});
}

function isStraight(allCardsRank) {
    const cardValues = [...new Set(allCardsRank.map(rank => ranks.indexOf(rank) + 2))].sort((a, b) => a - b);
    for (let i = 0; i <= cardValues.length - 5; i++) {
        if (cardValues.slice(i, i + 5).every((value, index, arr) => index === 0 || value === arr[index - 1] + 1)) {
            return true;
        }
    }
    return false;
}

function showCards() {
    bot1Score = checkValue(bot1, table);
    bot2Score = checkValue(bot2, table);
    bot3Score = checkValue(bot3, table);
    bot4Score = checkValue(bot4, table);
    playerScore = checkValue(player, table);

    bot1Section.innerHTML += ` Score: ${bot1Score}`;
    bot2Section.innerHTML += ` Score: ${bot2Score}`;
    bot3Section.innerHTML += ` Score: ${bot3Score}`;
    bot4Section.innerHTML += ` Score: ${bot4Score}`;
    playerSection.innerHTML += ` Score: ${playerScore}`;
}

play.addEventListener('click', () => {
    if(playerCoins > playValue.value){
        currentBet += parseInt(playValue.value);
        playerCoins -= playValue.value;
        bet.innerHTML = `${currentBet}`;
        playerCoinsSection.innerHTML = `(${playerCoins})`;
        tour();
    }

    if(table.length === 5)
        showCards();
    
});

fold.addEventListener('click', () => {

});

shuffle(cards);
dealingCards();

for (let i = 0; i < 3; i++) {
    tour();
}
