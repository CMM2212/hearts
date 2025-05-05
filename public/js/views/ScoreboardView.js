/**
 * Handles displaying the scoreboard UI at the end of a round.
 */
export default class ScoreboardView {
    /**
     * Constructs a new ScoreboardView with the given game model.
     * @param {GameModel} gameModel - The game model to use.
     */
    constructor(gameModel) {
        this.gameModel = gameModel;
        this.popup = $("#round-over");
        this.closeButton = this.popup.find('.resume-button');
        this.header = this.popup.find('.popup-header-round');
        this.table = this.popup.find('.popup-results');
        this.init();
    }

    /**
     * Initialize the scoreboard view by setting up event listeners.
     */
    init() {
        this.initEventListeners();
    }

    /**
     * Add event listeners for the close button to hide the scoreboard.
     */
    initEventListeners() {
        this.closeButton.on('click', () => this.hide());
    }

    /**
     * Update the scoreboard view to display the current scores of the player, and show the winner if the game is over.
     * @param {Array} playerData - The player data to display.
     * @param {boolean} gameOver - True if the game is over, false otherwise.
     */
    update(playerData, gameOver) {
        this.table.empty();

        const leaders = this.getLeaders(playerData);
        if (gameOver) {
            this.header.text(`Game Over! ${leaders[0]} wins!`)
            this.closeButton.hide();
        }

        playerData.forEach((player) => {
            const column = document.createElement("div");
            column.classList.add('popup-results-col');
            if (leaders.includes(player.name))
                column.classList.add('leading');
            const name = document.createElement("div");
            name.classList.add('popup-results-name');
            name.innerHTML = player.name;
            column.append(name);
            const score = document.createElement("div");
            score.classList.add('popup-results-score');
            score.innerHTML = player.score;
            column.append(score);
            this.table.append(column);
        });
    }

    /**
     * Returns a list of players with the lowest score.
     *
     * @param {Array} playerData - The player data to get the leaders from.
     * @returns {Array}
     */
    getLeaders(playerData) {
        const leaders = [];
        let minScore = Math.min(...playerData.map(player => player.score));
        playerData.forEach((player) => {
            if (player.score === minScore) {
                leaders.push(player.name);
            }
        });
        return leaders;
    }

    /**
     * Show the scoreboard.
     */
    show() {
        this.popup.show();
    }

    /**
     * Hide the scoreboard.
     */
    hide() {
        this.popup.hide();
    }
}
