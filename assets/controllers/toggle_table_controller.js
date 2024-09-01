import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["frame", "button"];
    static values = { full: String, short: String };

    connect() {
        console.log("Connected to toggle-table controller");

        if (!this.frameTarget) {
            console.error("Frame target is missing");
            return;
        }
        if (!this.buttonTarget) {
            console.error("Button target is missing");
            return;
        }

        console.log("Frame target:", this.frameTarget);
        console.log("Button target:", this.buttonTarget);

        this.updateButtonText();
    }

    toggle() {
        console.log("Toggle button clicked");

        const currentSrc = this.frameTarget.src || this.fullValue; // Default to fullValue if src is null
        const isFullVersion = currentSrc.includes(this.fullValue);
        const newPath = isFullVersion ? this.shortValue : this.fullValue;
        
        console.log(`Toggling to ${newPath}`);
        this.frameTarget.src = newPath;
        this.updateButtonText();
    }

    updateButtonText() {
        const currentSrc = this.frameTarget.src || this.fullValue; // Default to fullValue if src is null
        const isFullVersion = currentSrc.includes(this.fullValue);
        this.buttonTarget.innerText = isFullVersion ? "Към съкратената версия" : "Към пълната версия";
        console.log(`Button text updated to: ${this.buttonTarget.innerText}`);
    }
}