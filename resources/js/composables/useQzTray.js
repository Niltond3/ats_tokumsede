import { ref } from "vue";
import { qzTrayService } from "@/services/qzTrayService";

export function useQzTray() {
    const isConnected = ref(false);
    const selectedPrinter = ref(null);

    const connect = async () => {
        isConnected.value = await qzTrayService.connect();
        return isConnected.value;
    };

    const listPrinters = async () => {
        console.log("listPrinters");
        const printers = await qzTrayService.listPrinters();
        if (printers) return printers;
    };

    const findPrinter = async (name) => {
        console.log("findPrinter");
        const printer = await qzTrayService.findPrinter(name);
        if (printer) selectedPrinter.value = printer;
        return printer;
    };

    const print = async (data) => {
        if (!selectedPrinter.value) {
            throw new Error("No printer selected");
        }
        return await qzTrayService.print(selectedPrinter.value, data);
    };

    return {
        isConnected,
        selectedPrinter,
        connect,
        findPrinter,
        listPrinters,
        print,
    };
}
