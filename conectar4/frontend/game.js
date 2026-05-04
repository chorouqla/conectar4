
const urlParams = new URLSearchParams(window.location.search);
const gameId = urlParams.get('id');
const gameCode = urlParams.get('code');
const user = JSON.parse(localStorage.getItem('user'));

if (!user) window.location.href = 'index.html';
if (!gameId) window.location.href = 'lobby.html';

document.getElementById('gameCodeDisplay').innerHTML = `Game Code: ${gameCode}`;

let myTurn = false;
let myPlayerNum = null;
let gameActive = true;

async function api(url, data) {
    const res = await fetch("../backend/" + url, {
        method: data ? "POST" : "GET",
        body: data ? JSON.stringify(data) : null
    });
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch (e) {
        console.error("JSON error:", text);
        return {};
    }
}

async function loadGame() {
    const state = await api(`get_game_state.php?id=${gameId}`);
    if (!state) return;

    document.getElementById('player1Name').innerText = state.player1_name || 'Player 1';
    document.getElementById('player2Name').innerText = state.player2_name || 'Player 2';

    if (user.name == state.player1_name) myPlayerNum = 1;
    else if (user.name == state.player2_name) myPlayerNum = 2;

    myTurn = (state.turn == myPlayerNum);
    const turnEl = document.getElementById('turnIndicator');
    if (myTurn && gameActive) {
        turnEl.innerHTML = ' YOUR TURN! Click a column ';
        turnEl.style.color = '#4CAF50';
    } else if (gameActive) {
        turnEl.innerHTML = ' WAITING FOR OPPONENT... ';
        turnEl.style.color = '#ff9800';
    }

    if (state.board) drawBoard(state.board);

    if (state.winner) {
        gameActive = false;
        const winnerMsg = document.getElementById('winnerMessage');
        if (state.winner == myPlayerNum) {
            winnerMsg.innerHTML = '<div class="winner-banner win"> YOU WIN! </div>';
        } else {
            winnerMsg.innerHTML = '<div class="winner-banner lose"> YOU LOSE </div>';
        }
    }
}

function drawBoard(boardStr) {
    const boardDiv = document.getElementById('board');
    boardDiv.innerHTML = '';
    boardDiv.style.display = 'grid';
    boardDiv.style.gridTemplateColumns = 'repeat(7, 1fr)';
    boardDiv.style.gap = '10px';
    boardDiv.style.maxWidth = '600px';
    boardDiv.style.margin = '20px auto';

    for (let row = 0; row < 6; row++) {
        for (let col = 0; col < 7; col++) {
            const index = row * 7 + col;
            const value = boardStr[index];
            const cell = document.createElement('div');
            cell.className = 'cell';

            if (value == '1') {
                cell.textContent = '●';
                cell.style.color = '#ff4444';
                cell.style.textShadow = '0 0 10px #ff0000';
            } else if (value == '2') {
                cell.textContent = '●';
                cell.style.color = '#ffd700';
                cell.style.textShadow = '0 0 10px #ffaa00';
            } else {
                cell.textContent = '○';
                cell.style.color = '#2a1a4e';
            }

            if (gameActive && myTurn && value == '0') {
                cell.style.cursor = 'pointer';
                cell.onclick = (function (c) { return function () { makeMove(c); }; })(col);
            }

            boardDiv.appendChild(cell);
        }
    }
}

async function makeMove(column) {
    if (!myTurn || !gameActive) return;
    await api("make_move.php", { game_id: gameId, column: column, player_name: user.name });
    await loadGame();
}

function backToLobby() {
    window.location.href = 'lobby.html';
}

// start polling every 1 second
setInterval(loadGame, 1000);
loadGame();