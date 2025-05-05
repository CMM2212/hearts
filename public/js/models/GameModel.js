/**
 * Holds the state of the game and handles API calls to the server.
 */
export default class GameModel {
    /**
     * Creates a new game model.
     * @param {string} userId
     */
    constructor(userId) {
        this.userId = userId;
        this.isBusy = false;
        this.gamePlayerId = null;
    }

    /**
     * Send an API request to start the game and return a promise containing the response data which holds game state.
     * @returns {Promise} - Returns a promise that resolves when the game is started.
     */
    startGame() {
        console.log("Starting game request");
        // If another request is already in progress, return a rejected promise.
        if (this.isBusy)
            return $.Deferred().reject("Request is busy").promise();
        // Set the isBusy flag to true to prevent multiple requests.
        this.isBusy = true;
        return $.ajax(
            {
                url: "api/game/start",
                type: "POST",
                data: {
                    playerId: this.userId
                },
                dataType: "json"
            }
        ).always(() => this.isBusy = false); // Reset the isBusy flag when the request is complete.
    }

    /**
     * Sends API request to pass cards and return a promise containing the response data which holds game state.
     *
     * @param {Array} cards - The 3 cards the player will pass.
     * @returns {Promise} - Returns a promise that resolves when the cards are passed.
     */
    passCards(cards) {
        console.log("Passing cards request");
        // If another request is already in progress, return a rejected promise.
        if (this.isBusy)
            return $.Deferred().reject("Request is busy").promise();
        // Set the isBusy flag to true to prevent multiple requests.
        this.isBusy = true;
        return $.ajax(
            {
                url: "api/game/pass",
                type: "POST",
                data: {
                    playerId: this.gamePlayerId,
                    cards: cards
                },
                dataType: "json"
            }
        ).always(() => this.isBusy = false); // Reset the isBusy flag when the request is complete.
    }

    /**
     * Sends API request to play a card and return a promise containing the response data which holds game state.
     * @param {String} card - The card to be played.
     * @returns {Promise} - Returns a promise that resolves when the card is played.
     */
    playCard(card) {
        console.log("Playing card request");
        // If another request is already in progress, return a rejected promise.
        if (this.isBusy)
            return $.Deferred().reject("Request is busy").promise();
        // Set the isBusy flag to true to prevent multiple requests.
        this.isBusy = true;
        return $.ajax(
            {
                url: "api/game/play",
                type: "POST",
                data: {
                    playerId: this.gamePlayerId,
                    card: card
                },
                dataType: "json"
            }
        ).always(() => this.isBusy = false); // Reset the isBusy flag when the request is complete.
    }
}
