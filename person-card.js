class PersonCard {
  constructor(data) {
    this.name = data.name;
    this.title = data.title;
    this.affiliation = data.affiliation || '';
    this.image = data.image || null;
  }

  render(containerId) {
    const container = document.getElementById(containerId);
    
    const cardHTML = `
      <div class="person-card">
        <div class="person-card-img">
          ${this.image ? `<img src="${this.image}" alt="${this.name}">` : '👤'}
        </div>
        <div class="person-card-content">
          <div class="person-card-name">${this.name}</div>
          <div class="person-card-title">${this.title}</div>
          ${this.affiliation ? `<div class="person-card-affiliation">${this.affiliation}</div>` : ''}
        </div>
      </div>
    `;
    
    container.innerHTML += cardHTML;
  }

  static renderMultiple(persons, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    persons.forEach(personData => {
      const person = new PersonCard(personData);
      person.render(containerId);
    });
  }
}