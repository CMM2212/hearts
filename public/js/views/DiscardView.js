/**
 * Handles the cards currently being shown in the center of the table.
 */
export default class DiscardView {
    constructor(gameModel) {
        this.gameModel = gameModel;
        this.discardLeft = $("#discard-left");
        this.discardTop = $("#discard-top");
        this.discardRight = $("#discard-right");
        this.discardBottom = $("#discard-bottom");
        this.discards = [this.discardLeft, this.discardTop, this.discardRight, this.discardBottom];
    }

    /**
     * Updates the discard view by creating a card object for each card in the trick and appending it to the trick view.
     */
    update() {
        // const playersData = this.gameModel.gameState.playerData
        // for (let i = 0; i < playersData.length; i++) {
        //     const playerData = playersData[i];
        //     const discardElement = this.discards[i];
        //     // discardElement.empty();
        //     const discard = playerData.discarded;
        //     if (discard) {
        //         const cardElement = document.createElement("div");
        //         cardElement.classList.add("card");
        //         // cardElement.innerHTML = `<img src="images/cards/${discard.id}.svg" alt="${discard.rank} of ${discard.suit}">`
        //         discardElement.append(cardElement);
        //     }
        //
        //
        //     for (let j = 0; j < playerData.discardCount; j++) {
        //         const cardElement = document.createElement("div");
        //         cardElement.classList.add("card");
        //         cardElement.innerHTML = `<img src="images/cards/${playerData.discard[j]}.svg" alt="Hidden card">`
        //         discardElement.append(cardElement);
        //     }
        // }
    }

    hide() {
        this.discardLeft.hide();
        this.discardTop.hide();
        this.discardRight.hide();
        this.discardBottom.hide();
    }
}
