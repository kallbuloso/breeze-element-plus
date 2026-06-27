const defaultTitleByType = {
  success: 'Sucesso',
  warning: 'Atenção',
  error: 'Erro',
  info: 'Informação'
}

export const useToast = () => {
  const notify = (toast) => {
    if (!toast) return

    const type = toast.type ?? 'success'

    ElNotification({
      title: toast.title ?? defaultTitleByType[type] ?? '',
      message: toast.text ?? toast.message ?? '',
      type,
      duration: toast.duration ?? 3500,
      position: 'top-right'
    })
  }

  const flush = (toasts) => {
    if (Array.isArray(toasts)) toasts.forEach(notify)
  }

  return { notify, flush }
}
