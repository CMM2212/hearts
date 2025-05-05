/**
 * Handles the view of the hidden cards in the computer players' hands.
 */
export default class ComputerHandView {
    /**
     * Initializes the ComputerHandView with the game model.
     * @param {GameModel} gameModel - The game model to get the player data from.
     */
    constructor(gameModel) {
        this.gameModel = gameModel;
        this.computerLeft = $("#computer-1");
        this.computerRight = $("#computer-2");
        this.computerTop = $("#computer-3");
        this.humanName = $("#human").find(".player-name");
        this.computers = [this.computerLeft, this.computerRight, this.computerTop];
    }

    /**
     * Update each computer player's hand with the current game state, and update the names of all players.
     */
    update(playersData) {
        for (let i = 0; i < playersData.length; i++) {
            const playerData = playersData[i];
            if (playerData.isHuman) {
                this.humanName.text(playerData.name);
                continue;
            }
            const nameElement = this.computers[i].find(".player-name");
            const handElement = this.computers[i].find(".hand");
            handElement.empty();

            nameElement.text(playerData.name);
            // for (let j = 0; j < playerData.handCount; j++) {
            //     const cardElement = document.createElement("div");
            //     cardElement.classList.add("card");
            //     cardElement.innerHTML = `<img src="images/cards/0.svg" alt="Hidden card">`
            //     handElement.append(cardElement);
            // }
        }
    }
}
