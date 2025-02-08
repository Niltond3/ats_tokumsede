// security.js
export class PasswordGenerator {
    static getRandomChar(str) {
        return str.charAt(Math.floor(Math.random() * str.length));
    }

    static shuffle(array) {
        const newArray = [...array];
        for (let i = newArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
        }
        return newArray;
    }

    static generate(options = {}) {
        const groups = options.groups ?? ["ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz", "1234567890"];
        const length = options.length ?? 8;

        let pass = groups.map(this.getRandomChar).join("");
        const str = groups.join("");

        for (let i = pass.length; i < length; i++) {
            pass += this.getRandomChar(str);
        }

        return this.shuffle(pass.split("")).join("");
    }
}
