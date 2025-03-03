// Import necessary functions from Vue
import { ref, computed } from 'vue';

/**
 * Composable to handle distributor filtering logic.
 * @param {Ref<Array>} distribuidoresList - Reactive list of distributors.
 * @returns {Object} - Returns filterDistributorId, sortedDistribuidores, and formatDistributorName.
 */
export function useDistributorFilter(distribuidoresList) {
    // Reactive variable for the selected distributor filter (null means no filter applied)
    const filterDistributorId = ref(null);

    // Computed property to get a sorted list of distributors by their id
    const sortedDistribuidores = computed(() => {
        // Create a shallow copy of the distributor list and sort it by id
        return distribuidoresList.value.slice().sort((a, b) => a.id - b.id);
    });

    // Helper method to format distributor names by omitting the "TôKumSede " prefix
    const formatDistributorName = (name) => {
        const prefix = "TôKumSede ";
        // Check if the name starts with the prefix and remove it if true
        if (name.startsWith(prefix)) {
            return name.slice(prefix.length);
        }
        return name;
    };

    // Return the reactive variable, computed property, and helper function
    return { filterDistributorId, sortedDistribuidores, formatDistributorName };
}
