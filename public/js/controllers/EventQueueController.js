import {SETTINGS} from "../constants/settings.js";
import {EVENTS} from "../constants/events.js";

/**
 * Handles processing discards in a queue in order to animate the discards one at a time and simulate a real game flow.
 */
export default class EventQueueController {
    constructor() {
        this.discardLeft = $("#discard-left");
        this.discardTop = $("#discard-top");
        this.discardRight = $("#discard-right");
        this.discardBottom = $("#discard-bottom");

        this.computerLeft = $("#computer-1").find(".hand");
        this.computerRight = $("#computer-2").find(".hand");
        this.computerTop = $("#computer-3").find(".hand");
        this.humanHand = $("#human").find(".hand");

        this.hands = [this.computerLeft, this.computerTop, this.computerRight, this.humanHand];

        this.discards = [this.discardLeft, this.discardTop, this.discardRight, this.discardBottom];
        this.discardPile = $("#discards");
        this.directions = ["left", "top", "right", "bottom"];

        this.zIndexCounter = 2;

        this.init();
    }

    /**
     * Initialize the controller by setting up event listeners.
     */
    init() {
        this.initEventListeners();
    }

    /**
     * Initialize event listeners for the discard event.
     */
    initEventListeners() {
        document.addEventListener(EVENTS.DISCARD_CARD, (event) => this.playerDiscard(event["detail"]));
    }

    /**
     * Add the player's discarded card to the discard pile.
     * @param {string} card - The card to be discarded.
     */
    playerDiscard(card) {
        const parent = this.discardBottom;
        const cardElement = document.createElement("div");
        cardElement.classList.add("card");
        cardElement.style.zIndex = this.zIndexCounter++;
        cardElement.innerHTML = card.innerHTML;
        parent.append(cardElement);
    }


    update(history, callback) {
        this.processEventQueue(history, callback);
    }

    async processEventQueue(history, callback) {
        // Play all the discards for the previous trick
        for (const discard of history.previousDiscards) {
                await this.waitAfterAction(() => this.playDiscard(discard), SETTINGS.DISCARD_DELAY);
        }

        await this.waitAfterAction(() => {}, SETTINGS.DISCARD_DELAY);

        if (history.previousWinner) {
            await this.waitAfterAction(() => {}, SETTINGS.TRICK_DELAY);
            await this.waitAfterAction(() => this.endTrick(history.previousWinner), SETTINGS.DISCARD_DELAY * 2);
        }
        this.clearTrick();

        for (const discard of history.currentlyDiscarded)
            await this.waitAfterAction(() => this.playDiscard(discard), SETTINGS.DISCARD_DELAY);

        callback();
    }

    waitAfterAction(action, delay) {
        return new Promise(resolve => {
            action();
            setTimeout(() => {
                resolve();
            }, delay);
        });
    }

    playDiscard(discard) {
        this.removeCardFromHand(discard);
        this.createDiscardElement(discard)
    }

    endTrick(winner) {
        const direction = this.directions[winner.seat_number - 1];
        this.discardPile.addClass(`end-trick-animation-${direction}`);
    }

    clearTrick() {
        this.discardPile.removeClass("end-trick-animation-left");
        this.discardPile.removeClass("end-trick-animation-top");
        this.discardPile.removeClass("end-trick-animation-right");
        this.discardPile.removeClass("end-trick-animation-bottom");
        this.discards.forEach(discard => discard.empty());
        this.zIndexCounter = 2;
    }

    createDiscardElement(discard) {
        const parent = this.discards[discard.game_player.seat_number - 1];
        const cardElement = document.createElement("div");
        cardElement.classList.add("card");
        cardElement.style.zIndex = this.zIndexCounter++;
        cardElement.innerHTML = `<img src="images/cards/${discard.card.id}.svg" alt="${discard.card.rank} of ${discard.card.suit}">`
        parent.append(cardElement);
    }

    removeCardFromHand(discard) {
        const hand = this.hands[discard.game_player.seat_number - 1];
        if (discard.game_player.is_human === "0") {
            console.log("Child count before: ", hand.children().length);
            console.log("Removing card from hand", discard.card.id, discard.game_player.seat_number);
            hand.children().first().remove();
            console.log("Child count after: ", hand.children().length);
        }
    }
}

