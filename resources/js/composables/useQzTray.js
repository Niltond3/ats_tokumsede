import { ref } from "vue";
import { qzTrayService } from "@/services/qzTrayService";
import { errorUtils } from "@/util";

export function useQzTray() {
    const isConnected = ref(false);
    const selectedPrinter = ref("tks");

    const connect = async () => {
        try {
            isConnected.value = await qzTrayService.connect();
            return isConnected.value;
        } catch (error) {
            return error;
        }
    };

    const listPrinters = async () => {
        try {
            const printers = await qzTrayService.listPrinters();
            if (printers) return printers;
        } catch (error) {
            return error;
        }
    };
    const checkConnection = async () => {
        try {
            const response = await qzTrayService.checkConnection();
            isConnected.value = response;
            return response;
        } catch (error) {
            return error;
        }
    };
    const findPrinter = async (name) => {
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
        checkConnection,
        connect,
        findPrinter,
        listPrinters,
        print,
    };
}
