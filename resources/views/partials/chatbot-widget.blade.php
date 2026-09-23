{{-- Widget chatbot customer service, mengambang di kanan bawah semua halaman toko. --}}
<div
    x-data="{
        open: false,
        loading: false,
        input: '',
        messages: [
            { from: 'bot', text: 'Halo! Aku asisten SKANJAMart. Ada yang bisa aku bantu?', links: [], suggestions: [] },
        ],
        send(text) {
            var msg = (text !== undefined ? text : this.input).trim();
            if (!msg || this.loading) return;

            this.messages.push({ from: 'user', text: msg });
            this.input = '';
            this.loading = true;
            this.scrollDown();

            fetch({{ Js::from(route('chatbot.ask')) }}, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message: msg }),
            }).then(function (res) { return res.json(); }).then((json) => {
                this.messages.push({ from: 'bot', text: json.reply, links: json.links || [], suggestions: json.suggestions || [] });
            }).catch(() => {
                this.messages.push({ from: 'bot', text: 'Maaf, ada gangguan koneksi. Coba lagi sebentar ya.', links: [], suggestions: [] });
            }).finally(() => {
                this.loading = false;
                this.scrollDown();
            });
        },
        scrollDown() {
            this.$nextTick(() => { var el = this.$refs.log; if (el) el.scrollTop = el.scrollHeight; });
        },
    }"
    class="fixed bottom-5 right-5 z-50"
>
    <button
        @click="open = !open"
        class="w-14 h-14 rounded-full bg-forest-500 text-canvas shadow-soft flex items-center justify-center text-2xl hover:bg-forest-600 transition"
        aria-label="Buka chat customer service"
    >
        <span x-show="!open">💬</span>
        <span x-show="open" x-cloak>✕</span>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute bottom-[4.5rem] right-0 w-[calc(100vw-2.5rem)] max-w-sm h-[28rem] bg-white rounded-2xl shadow-soft border border-forest-100 flex flex-col overflow-hidden"
    >
        <div class="bg-forest-700 text-canvas px-4 py-3 shrink-0">
            <p class="font-display font-medium">Customer Service SKANJAMart</p>
            <p class="text-xs text-canvas/70">Biasanya balas seketika</p>
        </div>

        <div x-ref="log" class="flex-1 overflow-y-auto px-4 py-3 space-y-3 text-sm">
            <template x-for="(m, i) in messages" :key="i">
                <div :class="m.from === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        class="max-w-[85%] rounded-2xl px-3.5 py-2.5 whitespace-pre-line"
                        :class="m.from === 'user' ? 'bg-forest-500 text-canvas rounded-br-sm' : 'bg-forest-50 text-ink rounded-bl-sm'"
                    >
                        <span x-text="m.text"></span>
                        <template x-if="m.links && m.links.length">
                            <div class="mt-2 flex flex-col gap-1.5">
                                <template x-for="link in m.links" :key="link.url">
                                    <a :href="link.url" target="_blank" class="text-xs font-semibold text-forest-700 bg-white border border-forest-200 rounded-full px-3 py-1.5 text-center hover:bg-forest-50" x-text="link.label"></a>
                                </template>
                            </div>
                        </template>
                        <template x-if="m.suggestions && m.suggestions.length">
                            <div class="mt-2 flex flex-col gap-1.5">
                                <template x-for="s in m.suggestions" :key="s">
                                    <button type="button" @click="send(s)" class="text-xs text-left text-forest-700 bg-white border border-forest-200 rounded-full px-3 py-1.5 hover:bg-forest-50" x-text="s"></button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="bg-forest-50 text-ink-faint rounded-2xl rounded-bl-sm px-3.5 py-2.5 text-xs">Mengetik...</div>
            </div>
        </div>

        <form @submit.prevent="send()" class="border-t border-forest-100 p-3 flex gap-2 shrink-0">
            <input
                type="text"
                x-model="input"
                placeholder="Tulis pertanyaanmu..."
                maxlength="500"
                class="input-classic text-sm py-2 flex-1"
            >
            <button type="submit" :disabled="loading || !input.trim()" class="btn-primary !py-2 !px-4 text-sm disabled:opacity-50">Kirim</button>
        </form>
    </div>
</div>
