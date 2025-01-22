export default function observeStockChanges(callback) {
    return () => {
        callback();
    };
}
