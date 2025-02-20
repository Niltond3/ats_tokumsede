import { ErrorUtil } from "@/util";
import { ref } from "vue";
import { toast } from "vue-sonner";
import audio from "@/Layouts/config/audio";
import { getLastOrder } from "@/services/api/orders";

const ultimoPedido = ref(null);
const callback = ref(null);
const jqueryStringTab = "[id*=-trigger-pedidos]";
const jqueryStringNotificationBadge = `${jqueryStringTab}>span>span>div`;

function playSound() {
    if (audio) {
        audio.autoplay = true;
        const audioState = $("#toggleSound").attr("data-state");
        const setAudio = {
            on: () => (audio.volume = 1),
            off: () => (audio.volume = 0),
        };

        setAudio[audioState]();

        audio.load();
        audio.play();
    }
}
const newOrder = async () => {
    try {
        const response = await getLastOrder()

        if (response.data !== ultimoPedido.value)
            ultimoPedido.value = response.data;
    } catch (error) {
        toast.error(ErrorUtil.getError(error));
    }
};

const getNewOrders = async (successCallback) => {
    try {
        const response = await getNewOrders(ultimoPedido.value)
        const novosPedidos = response.data;

        if (novosPedidos.length > 0) {
            successCallback(novosPedidos.length);
        }
    } catch (error) {
        toast.error(ErrorUtil.getError(error));
    }
};

const isActive = async () => {
    const notificationBadge = $(jqueryStringNotificationBadge);

    !notificationBadge.hasClass("hidden") &&
        notificationBadge.addClass("hidden");

    !ultimoPedido.value && newOrder();

    const getSucessCallback = async () => {
        await newOrder();
        callback.value();
        playSound();
    };
    getNewOrders(getSucessCallback);
};

const isInactive = async () => {
    const notificationBadge = $(jqueryStringNotificationBadge);
    !ultimoPedido.value && (await newOrder());

    const successCallback = (lenght) => {
        if (notificationBadge.hasClass("hidden")) {
            notificationBadge.removeClass("hidden");
            notificationBadge.addClass("flex");
        }
        notificationBadge.html(lenght);
        playSound();
    };
    getNewOrders(successCallback);
};

export default function (props) {
    const tab = $(jqueryStringTab).attr("data-state");
    if (props) callback.value = props;
    const activeOrNot = {
        active: isActive,
        inactive: isInactive,
    };
    if (tab) return activeOrNot[tab]();
    console.error("sem tab: implementar para outros casos de uso");
}
