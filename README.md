# DoodleHub

**A full-stack drawing app I built on a random Wednesay using PHP and MySQL.**
**Doing a lot of PHP in my college course atm, so thought I'd make something using what I've learnt**

![demo](https://github.com/user-attachments/assets/5d6f5e89-77dc-4f6a-93ef-32a88ca6eb78)

### What it does
- Draw anything with colour +  brush size  
- Touch-friendly (works on phone)  
- Every stroke saved as JSON → perfect replay in gallery  
- Gallery which showcases all saved drawings

### Tech stack (all vanilla)
- PHP 8 
- MySQL 
- HTML5 
- Tailwind CDN 

### Why I built it
Wanted a portfolio project that actually shows I can:
- Ship a complete full-stack app solo
- Store and replay complex JSON without issues
- Make something that looks and feels somewhat professional

### Local setup (if you wanna run it)
```bash
git clone https://github.com/0xWiIIiam/doodlehub.git
cd doodlehub
# put files on XAMPP/laragon htdocs or any PHP server
# import drawings table:
CREATE TABLE drawings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT 'Untitled',
    data LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
# open http://localhost/doodlehub
