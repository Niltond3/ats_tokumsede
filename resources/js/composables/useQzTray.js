import { ref } from "vue";
import { qzTrayService } from "@/services/printer/qzTrayService";

export function useQzTray() {
    const isConnected = ref(false);
    const selectedPrinter = ref("tks");

    const connect = async () => {
        const response = await qzTrayService.connect();
        if (response) {
            isConnected.value = true;
        } else {
            isConnected.value = false;
            throw new Error("Impossível conectar com a impressora");
        }
    };

    const listPrinters = async () => {
        const printers = await qzTrayService.listPrinters();
        if (printers) return printers;
    };
    const checkConnection = async () => {
        const response = await qzTrayService.checkConnection();
        isConnected.value = response;
        return response;
    };
    const findPrinter = async (name) => {
        try {
            const printer = await qzTrayService.findPrinter(name);
            if (printer) selectedPrinter.value = printer;
            return printer;
        } catch (error) {
            throw new Error(error);
        }
    };

    const print = async (data) => {
        if (!selectedPrinter.value) {
            throw new Error("Nenhuma impressora selecionada");
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
