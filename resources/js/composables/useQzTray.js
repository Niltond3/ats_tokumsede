import { ref } from "vue";
import { qzTrayService } from "@/services/qzTrayService";

export function useQzTray() {
    const isConnected = ref(false);
    const selectedPrinter = ref("tks");

    const connect = async () => {
        isConnected.value = await qzTrayService.connect();
        return isConnected.value;
    };

    const listPrinters = async () => {
        const printers = await qzTrayService.listPrinters();
        console.log(printers);
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
            console.log(printer);
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
