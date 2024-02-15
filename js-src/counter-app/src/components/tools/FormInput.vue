<template>
  <form @submit="$emit('submitForm', null)">
    <dl>
      <template v-if="settings.labels">
        <template v-for="(entry, index) in settings.labels">
          <template v-if="settings.labels[index]">
            <dt>
              {{ settings.labels[index] }}
            </dt>
            <dd>
              {{ data[index] }}
            </dd>
          </template>
        </template>
      </template>
      <template v-else>
        <template v-for="(entry, index) in data">
          <dt>
            {{ index }}
          </dt>
          <dd>
            {{ entry }}
          </dd>
        </template>
      </template>
    </dl>
  </form>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";

export default defineComponent({
  name: 'FormInput',
  components: { RouterLink },
  props: {
    data: { required: true, type: Object },
    settings: { required: true, type: Object },
  },
  setup: (props) => {
    const data = computed(() => {
      return props.data;
    });
    const settings = computed(() => {
      console.log("settings", props.settings);
      return props.settings;
    });

    return { data, settings }
  },
  watch: {
    settings: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: headers", newValue, oldValue)
      }
    }
  }
});
</script>
<style scoped></style>
