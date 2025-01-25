<script setup>
import { ref, computed, onMounted } from 'vue';
import { useReminders } from '@/composables/useReminders';
import { useReminderActions } from '@/composables/useReminderActions';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import ReminderForm from './ReminderForm.vue';
import draggable from 'vuedraggable';

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

const searchQuery = ref('');
const editingIndex = ref(null);
const { reminders, isLoading, currentPage, totalPages, activeFilters, fetchReminders } =
  useReminders(props.clientId);
const { deleteReminder } = useReminderActions();

onMounted(() => fetchReminders(1));

const filteredAndGroupedReminders = computed(() => {
  const filtered = reminders.value.filter((reminder) =>
    reminder.descricao.toLowerCase().includes(searchQuery.value.toLowerCase()),
  );

  return {
    Ativos: filtered.filter((r) => r.status === 'ATIVO'),
  };
});

const handleDelete = async (id) => {
  await deleteReminder(id);
  fetchReminders(currentPage.value);
};

const handleDragEnd = async (evt) => {
  await fetchReminders(currentPage.value);
};

const handleEdit = (index) => {
  editingIndex.value = index;
};

const handleSaved = () => {
  editingIndex.value = null;
  fetchReminders(currentPage.value);
};
</script>

<template>
  <div class="space-y-4">
    <Input
      v-model="searchQuery"
      placeholder="Buscar lembretes..."
      class="bg-white/10 border-white/20 text-white placeholder:text-white/60"
    />

    <div v-if="isLoading" class="space-y-2">
      <Card v-for="n in 3" :key="n" class="animate-pulse">
        <CardContent class="h-24 bg-muted/50" />
      </Card>
    </div>

    <TransitionGroup v-else name="list" tag="div" class="space-y-6">
      <template v-for="(reminders, status) in filteredAndGroupedReminders" :key="status">
        <div>
          <h3 class="text-xl font-display font-semibold text-white mb-4 capitalize">
            {{ status }}
          </h3>

          <draggable
            :model-value="reminders"
            group="reminders"
            item-key="id"
            class="space-y-2"
            @end="handleDragEnd"
          >
            <template #item="{ element: reminder, index }">
              <div class="transition-all duration-300 ease-in-out">
                <Transition name="fade" mode="out-in">
                  <ReminderForm
                    v-if="editingIndex === index"
                    :reminder="reminder"
                    :client-id="clientId"
                    :client-name="clientName"
                    class="bg-white/10 rounded-lg shadow-lg"
                    @saved="handleSaved"
                    @cancel="editingIndex = null"
                  />
                  <Card v-else class="bg-white/10 hover:bg-white/20 transition-colors duration-300">
                    <CardContent class="p-4 flex justify-between items-start">
                      <div>
                        <p class="font-medium text-white">{{ reminder.descricao }}</p>
                        <p v-if="reminder.data_limite" class="text-sm text-white/70">
                          Prazo: {{ new Date(reminder.data_limite).toLocaleDateString() }}
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
                          @click="handleDelete(reminder.id)"
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

    <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-4">
      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage === 1"
        @click="fetchReminders(currentPage - 1)"
      >
        Anterior
      </Button>
      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage === totalPages"
        @click="fetchReminders(currentPage + 1)"
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
