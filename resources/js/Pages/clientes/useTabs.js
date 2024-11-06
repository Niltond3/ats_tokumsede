import { ref } from 'vue'

export function useToggleTabs(defaultTab = 'clientes') {

    const activeTab = ref(defaultTab);

    function setActiveTab(index) {
        activeTab.value = index;
    }

    return { activeTab, setActiveTab };

}
