import base64string from "./base64string";

var Sound = (function () {
    var df = document.createDocumentFragment();
    return function Sound(src) {
        var snd = new Audio(src);
        df.appendChild(snd); // keep in fragment until finished playing
        snd.addEventListener('ended', function () { df.removeChild(snd); });
        snd.play();
        return snd;
    }
}());

export default Sound("data:audio/wav;base64," + base64string);
