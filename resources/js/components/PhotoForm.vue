<template>
  <div v-show="value" class="photo-form">
    <h2 class="title">Submit a photo</h2>
    <form class="form" @submit.prevent="submit">
      <input class="form__item" type="file" @change="onFileChange" />
      <output class="form__output" v-if="preview">
        <img :src="preview" alt="" />
      </output>
      <div class="form__button">
        <button type="submit" class="button button--inverse">submit</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      preview: null,
      photo: null,
    };
  },
  methods: {
    // フォームでファイルが選択されたら実行される
    onFileChange(event) {
      // 何も選択されていなかったら処理中断
      if (event.target.files.length === 0) {
        this.reset();
        return false;
      }

      // ファイルが画像ではなかったら処理中断
      if (!event.target.files[0].type.match("image.*")) {
        this.reset();
        return false;
      }

      // FileReaderクラスのインスタンスを取得
      const reader = new FileReader();

      // ファイルを読み込み終わったタイミングで実行する処理
      reader.onload = (e) => {
        this.preview = e.target.result;
      };

      // ファイルを読み込む
      // 読み込まれたファイルはデータURL形式で受け取れる
      reader.readAsDataURL(event.target.files[0]);
      this.photo = event.target.files[0];
    },
    // 入力欄の値とプレビュー表示をクリアするメソッド
    reset() {
      this.preview = "";
      this.photo = null;
      this.$el.querySelector('input[type="file"]').value = null;
    },
    async submit() {
      const formData = new FormData();
      formData.append("photo", this.photo);
      const response = await axios.post("/photos", formData);

      this.reset();
      this.$emit("input", false);
      this.$router.push(`/photos/${response.data.id}`);
    },
  },
};
</script>