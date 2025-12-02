import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'

let md: MarkdownIt | null = null

function getMarkdownInstance(): MarkdownIt {
    if (!md) {
        md = new MarkdownIt({
            html: true,
            breaks: true,
            highlight: function (str: string, lang: string): string {
                if (lang && hljs.getLanguage(lang)) {
                    try {
                        return `<pre class="hljs rounded-lg p-4 overflow-x-auto"><code class="language-${lang}">${
                            hljs.highlight(str, { language: lang, ignoreIllegals: true }).value
                        }</code></pre>`
                    } catch (_) {}
                }
                return `<pre class="hljs rounded-lg p-4 overflow-x-auto"><code>${md!.utils.escapeHtml(str)}</code></pre>`
            }
        })
    }
    return md
}

export function useMarkdown() {
    function renderMarkdown(content: string): string {
        if (!content) return ''
        return getMarkdownInstance().render(content)
    }

    return {
        renderMarkdown,
    }
}