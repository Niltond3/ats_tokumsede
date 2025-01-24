<script setup>
import { ref } from 'vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import ReminderList from './ReminderList.vue';
import ReminderForm from './ReminderForm.vue';
import { Button } from '@/components/ui/button';

const props = defineProps({
  clientId: {
    type: Number,
    required: true,
  },
  clientName: {
    type: String,
    required: true,
  },
});

const isOpen = ref(false);
const isCreating = ref(false);
const selectedReminder = ref(null);

const toggleDialog = () => {
  isOpen.value = !isOpen.value;
  if (!isOpen.value) {
    isCreating.value = false;
    selectedReminder.value = null;
  }
};

defineExpose({ toggleDialog });
</script>

<template>
  <div>
    <Button class="fixed bottom-6 right-5 rounded-full size-14 p-0 shadow-lg" @click="toggleDialog">
      <i class="ri-alarm-line text-xl" />
    </Button>

    <Dialog :open="isOpen" @update:open="toggleDialog">
      <DialogContent class="sm:max-w-[425px]">
        <div class="flex flex-col gap-4">
          <header class="flex justify-between items-center text-info">
            <h2 class="text-lg font-semibold">
              {{ isCreating ? 'Novo Lembrete' : 'Lembretes' }}
            </h2>
            <Button
              v-if="!isCreating"
              class="size-9 flex items-center justify-center rounded-full p-0 mx-2"
              @click="isCreating = true"
            >
              <i class="ri-add-line" />
            </Button>
          </header>

          <ReminderForm
            v-if="isCreating"
            :reminder="selectedReminder"
            :client-id="clientId"
            :client-name="clientName"
            @saved="toggleDialog"
            @cancel="isCreating = false"
          />
          <ReminderList
            v-else
            :client-id="clientId"
            @edit="
              (reminder) => {
                selectedReminder = reminder;
                isCreating = true;
              }
            "
          />
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>
