<template>
  <form
    @submit.prevent="
      dirty = false;
      $emit('submitForm', null);
    "
  >
    <dl>
      <template v-for="(item, index) of data">
        <template v-if="settings.writable[index]">
          <dt>
            <label :for="'data_field_' + index">
              {{ settings.labels[index] }}
            </label>
          </dt>
          <dd>
            <!-- inputs with special requirements -->
            <input
              v-if="'decimal' === settings.writable[index]"
              :id="'data_field_' + index"
              type="number"
              :step="settings.attributes[index].step ?? 1"
              v-model="data[index]"
            />
            <textarea
              v-if="'textarea' === settings.writable[index]"
              :id="'data_field_' + index"
              v-model="data[index]"
            >
            </textarea>
            <select
              v-if="'select' === settings.writable[index]"
              :id="'data_field_' + index"
              v-model="data[index]"
            >
              <option v-for="(text,value) of settings.attributes[index].options" :value="value">
                {{ text }}
              </option>
            </select>
            <!-- if no special handling for input is required -->
            <input
              v-if="0 > special_fields.indexOf(settings.writable[index])"
              :id="'data_field_' + index"
              :type="settings.writable[index]"
              v-model="data[index]"
            />
          </dd>
        </template>
      </template>
    </dl>
    <div class="submit-div">
      <RouterLink v-if="abort" class="button" :to="abort">{{
        settings.labelBackBtn
      }}</RouterLink>
      <input
        v-if="true === dirty"
        type="submit"
        :value="settings.labelSubmitBtn"
      />
      <input
        v-if="true !== dirty"
        type="submit"
        disabled
        :value="settings.labelSubmitBtn"
      />
    </div>
  </form>
</template>
<script>
import { computed, defineComponent, ref } from "vue";

export default defineComponent({
  name: "UniversalForm",
  components: {},
  props: {
    abort: { type: Object },
    data: { required: true, type: Object },
    settings: { required: true, type: Object },
  },
  dirty: false,
  setup: (props) => {
    const defaultSettings = {
      labelSubmitBtn: "Änderungen speichern",
      labelBackBtn: "zurück",
    };
    const settings = computed(() => {
      console.log("settings", props.settings);
      return { ...defaultSettings, ...props.settings };
    });

    const abort = ref(props.abort || null);

    const data = ref(props.data);
    // const dirty = ref(dirty);

    const data2 = computed({
      get: () => {
        // dirty = false;
        return props.data;
      },
      set: (newValue) => {
        props.data = newValue;
      },
    });
    console.log("abort", props.abort);

    const special_fields = ["date", "decimal", "textarea", "select"];
    return { abort, data, settings, special_fields };
  },
  watch: {
    data: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: headers", newValue, oldValue);
        this.dirty = true;
      },
    },
  },
});
</script>
<style scoped>
.submit-div {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #aaa;
  text-align: right;
}

input:disabled,
input[disabled] {
  background-color: #eee;
  color: #ccc;
  border-color: #ccc;
}
</style>
