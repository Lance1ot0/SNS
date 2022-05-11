const getFormData = (formElement) => {
  const formData = {}
  
  if (!formElement) return formData
  
  const formElements = [...formElement.elements]

  formElements.forEach(element => {
    if (element.name) {
      formData[element.name] = element.value
    }
  })

  return formData  
}

export default getFormData