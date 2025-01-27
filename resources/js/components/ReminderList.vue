<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useReminders } from '@/composables/useReminders';
import { useReminderActions } from '@/composables/useReminderActions';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import ReminderForm from './ReminderForm.vue';
import draggable from 'vuedraggable';

const props = defineProps({
  reminders: { type: Array, required: false, default: null },
  observeNewReminder: { type: null, required: true },
  clientId: {
    type: Number,
    required: true,
  },
  clientName: {
    type: String,
    required: true,
  },
});

const searchQuery = ref('');
const editingIndex = ref(null);

const localReminders = ref([]);
const localIsLoading = ref(false);
const localCurrentPage = ref(1);
const localTotalPages = ref(1);
const localFetchingReminders = ref(null);

const loadLocalReminders = async (clientId) => {
  if (clientId !== null && clientId !== undefined) {
    const { reminders, isLoading, currentPage, totalPages, rawReminders, fetchReminders } =
      useReminders(clientId);

    localFetchingReminders.value = fetchReminders;

    await fetchReminders().then((count) => {
      // Sync values with local refs
      localReminders.value = reminders.value;
      localIsLoading.value = isLoading.value;
      localCurrentPage.value = currentPage.value;
      localTotalPages.value = totalPages.value;
    });
  }
};

watch(
  () => props.observeNewReminder,
  async (newVal) => {
    await loadLocalReminders(props.clientId);
  },
);

onMounted(async () => {
  await loadLocalReminders(props.clientId);
});

const emit = defineEmits(['delete', 'reminderUpdated']);

const { deleteReminder } = useReminderActions();

const filteredAndGroupedReminders = computed(() => {
  const filtered = localReminders.value.filter((reminder) =>
    reminder.descricao.toLowerCase().includes(searchQuery.value.toLowerCase()),
  );

  return {
    Ativos: filtered.filter((r) => r.status === 'ATIVO'),
  };
});

const handleDelete = async (id) => {
  try {
    await deleteReminder(id);
    await loadLocalReminders(props.clientId);
    emit('delete', localReminders.value.length);
  } catch (error) {
    console.error('Erro ao excluir lembrete:', error);
  }
};

const handleDragEnd = async (evt) => {
  await fetchReminders(localCurrentPage.value);
};

const handleEdit = (index) => {
  editingIndex.value = index;
};

const handleSaved = async () => {
  editingIndex.value = null;
  await loadLocalReminders(props.clientId);
  emit('reminderUpdated');
};
</script>

<template>
  <div class="space-y-4">
    <Input
      v-model="searchQuery"
      placeholder="Buscar lembretes..."
      class="bg-white/10 border-white/20 text-white placeholder:text-white/60"
    />

    <div v-if="localIsLoading" class="space-y-2">
      <Card v-for="n in 3" :key="n" class="animate-pulse">
        <CardContent class="h-24 bg-muted/50" />
      </Card>
    </div>

    <TransitionGroup v-else name="list" tag="div" class="space-y-6">
      <template v-for="(filteredReminders, status) in filteredAndGroupedReminders" :key="status">
        <div>
          <h3 class="text-xl font-display font-semibold text-white mb-4 capitalize">
            {{ status }}
          </h3>

          <draggable
            :model-value="filteredReminders"
            group="reminders"
            item-key="id"
            class="space-y-2"
            @end="handleDragEnd"
          >
            <template #item="{ element: filteredReminders, index }">
              <div class="transition-all duration-300 ease-in-out">
                <Transition name="fade" mode="out-in">
                  <ReminderForm
                    v-if="editingIndex === index"
                    :reminder="filteredReminders"
                    :client-id="clientId"
                    :client-name="clientName"
                    class="bg-white/10 rounded-lg shadow-lg"
                    @saved="handleSaved"
                    @cancel="editingIndex = null"
                  />
                  <Card v-else class="bg-white/10 hover:bg-white/20 transition-colors duration-300">
                    <CardContent class="p-4 flex justify-between items-start">
                      <div>
                        <p class="font-medium text-white">{{ filteredReminders.descricao }}</p>
                        <p v-if="filteredReminders.data_limite" class="text-sm text-white/70">
                          Prazo: {{ new Date(filteredReminders.data_limite).toLocaleDateString() }}
                        </p>
                      </div>
                      <div class="flex gap-2">
                        <Button
                          variant="ghost"
                          size="icon"
                          class="text-white hover:bg-white/20"
                          @click="handleEdit(index)"
                        >
                          <i class="ri-edit-line" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          class="text-destructive hover:bg-destructive/20"
                          @click="handleDelete(filteredReminders.id)"
                        >
                          <i class="ri-delete-bin-line" />
                        </Button>
                      </div>
                    </CardContent>
                  </Card>
                </Transition>
              </div>
            </template>
          </draggable>
        </div>
      </template>
    </TransitionGroup>

    <div v-if="localTotalPages > 1" class="flex justify-center gap-2 mt-4">
      <Button
        variant="outline"
        size="sm"
        :disabled="localCurrentPage === 1"
        @click="localFetchingReminders(localCurrentPage - 1)"
      >
        Anterior
      </Button>
      <Button
        variant="outline"
        size="sm"
        :disabled="localCurrentPage === localTotalPages"
        @click="localFetchingReminders(localCurrentPage + 1)"
      >
        Próxima
      </Button>
    </div>
  </div>
</template>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
