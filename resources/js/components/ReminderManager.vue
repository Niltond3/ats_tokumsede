<script setup>
import { ref, watch } from 'vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import ReminderList from './ReminderList.vue';
import ReminderForm from './ReminderForm.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useReminders } from '@/composables/useReminders';

const props = defineProps({
  reminders: { type: Array, required: false, default: () => [] },
  clientId: {
    type: Number,
    required: true,
  },
  clientName: {
    type: String,
    required: true,
  },
});

const localReminders = ref([]);
const localIsLoading = ref(false);
const localCurrentPage = ref(1);
const localTotalPages = ref(1);
const localActiveFilters = ref({ status: 'ATIVO' });
const localActiveCount = ref(0);
const isOpen = ref(false);
const isCreating = ref(false);
const reminderBeCreated = ref(false);

const loadLocalReminders = async (clientId) => {
  if (clientId !== null && clientId !== undefined) {
    const {
      reminders,
      isLoading,
      currentPage,
      totalPages,
      activeFilters,
      activeRemindersCount,
      fetchReminders,
    } = useReminders(clientId);

    await fetchReminders().then((count) => {
      // Sync values with local refs
      localReminders.value = reminders;
      localIsLoading.value = isLoading;
      localCurrentPage.value = currentPage;
      localTotalPages.value = totalPages;
      localActiveFilters.value = activeFilters;
      localActiveCount.value = count;
    });
  }
};

watch(
  () => props.clientId,
  async (newClientId) => {
    await loadLocalReminders(newClientId);
  },
  { immediate: true },
);

const handleReminderUpdate = async (createNewReminder) => {
  isCreating.value = false;
  if (createNewReminder) reminderBeCreated.value = createNewReminder;
  await loadLocalReminders(props.clientId);
};

const toggleDialog = () => (isOpen.value = !isOpen.value);

defineExpose({ toggleDialog });
</script>

<template>
  <div>
    <Button class="fixed bottom-6 right-5 rounded-full size-14 p-0 shadow-lg" @click="toggleDialog">
      <i class="ri-alarm-line text-xl" />
      <Badge
        v-if="localActiveCount > 0"
        class="absolute flex items-center justify-center -top-1 -right-1 bg-info ring-2 ring-white text-white p-0 !size-5"
      >
        {{ localActiveCount }}
      </Badge>
    </Button>

    <Dialog
      :open="isOpen"
      class="transition-all duration-300 ease-in-out"
      @update:open="toggleDialog"
    >
      <DialogContent class="sm:max-w-[800px] bg-gradient-to-br from-info to-info/80">
        <div class="flex flex-col gap-4">
          <header class="flex justify-between items-center text-white">
            <h2 class="text-2xl font-display font-bold">Lembretes</h2>
            <Button
              v-if="!isCreating"
              class="flex items-center justify-center rounded-sm px-2 py-1 mx-2 transition-opacity duration-300"
              @click="isCreating = true"
            >
              <i class="ri-add-line" />
              Novo Lembrete
            </Button>
          </header>

          <div class="bg-white/10 backdrop-blur-sm p-4 rounded-lg">
            <div
              class="transition-all duration-300 ease-in-out overflow-hidden"
              :class="isCreating ? 'max-h-[500px] opacity-100' : 'max-h-0 opacity-0'"
            >
              <ReminderForm
                :client-id="clientId"
                :client-name="clientName"
                @saved="handleReminderUpdate"
                @cancel="isCreating = false"
              />
            </div>
            <ReminderList
              :client-id="clientId"
              :client-name="clientName"
              :observe-new-reminder="reminderBeCreated"
              @reminder-updated="handleReminderUpdate"
              @delete="handleReminderUpdate"
            />
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style>
.font-display {
  font-family: 'Poppins', sans-serif;
}
</style>
