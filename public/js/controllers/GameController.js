import {EVENTS} from "../constants/events.js";

export default class GameController {
    constructor(gameModel, passView, trickView, discardView, computerHanDView, scoreboardView,
                eventQueueController) {
        this.gameModel = gameModel;
        this.passView = passView;
        this.trickView = trickView;
        this.discardView = discardView;
        this.computerHandView = computerHanDView;
        this.scoreboardView = scoreboardView;
        this.eventQueueController = eventQueueController;
        this.clickBlocker = $("#click-blocker");
        this.init();
    }

    init() {
        this.passView.hide();
        this.trickView.hide();
        this.enableClicks();
        this.initEventListeners();
        this.startGame()
    }

    initEventListeners() {
        document.addEventListener(EVENTS.START_GAME, (event) => this.startGame(event["detail"]["playerId"]));
        document.addEventListener(EVENTS.PASS_CARDS, (event) => this.passCards(event["detail"]));
        document.addEventListener(EVENTS.PLAY_CARD, (event) => this.playCard(event["detail"]));
    }

    startGame() {
        console.log("Starting game");
        this.gameModel.startGame()
            .done(response => {
                if (response) {
                    this.handleResponse(response);
                }
            }).fail(error => console.error(error));
    }

    passCards(cards) {
        this.disableClicks();
        this.gameModel.passCards(cards['cardIds'])
            .done(response => {
                console.log("Passing cards response", response)
                if (response) {
                    this.handleResponse(response);
                }
            }).fail(error => console.error(error));
    }

    playCard(card) {
        this.disableClicks();
        this.gameModel.playCard(card)
            .done(response => {
                console.log("Playing card response", response)
                if (response) {
                    this.handleResponse(response);
                }
            }).fail(error => console.error(error));
    }

    handleResponse(response) {
        console.log("Handling response", response);
        this.gameModel.gamePlayerId = response.gamePlayerId;
        if (response.state === "passing")
            this.eventQueueController.update(response.history, () => this.updatePassingState(response));
        else if (response.state === "trick")
            this.eventQueueController.update(response.history, () => this.updateTrickState(response));
        else if (response.state === "end")
            this.updateEndState(response);
        else
            console.error("Unknown game state", response.state);
    }
    updatePassingState(data) {
        this.enableClicks();

        if (data.roundChanged) {
            this.scoreboardView.update(data.playersData);
            this.scoreboardView.show();
        }
        this.passView.update(data.cardHands);
        this.computerHandView.update(data.playersData);
        this.discardView.update(data.playersData);

    }

    updateTrickState(data) {
        this.enableClicks();

        if (data.roundChanged) {
            this.scoreboardView.update(data.playersData);
            this.scoreboardView.show();
        }
        this.passView.hide();
        this.trickView.update(data.cardHands);
        this.computerHandView.update(data.playersData);
        this.discardView.update(data.playersData);

    }

    updateEndState(data) {
        this.passView.hide();
        this.scoreboardView.update(data.playersData, true);
        this.trickView.update(data.cardHands);
        this.discardView.hide();
        this.scoreboardView.show();
    }

    enableClicks() {
        this.clickBlocker.hide();
    }

    disableClicks() {
        this.clickBlocker.show();
    }
}
