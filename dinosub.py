import os
import requests
from concurrent.futures import ThreadPoolExecutor as Pool

def install_required_modules():
    [__import__(m) for m in ["requests"] if not __import__(m, globals(), locals(), [], 0)]

def clear_terminal():
    os.system("cls" if os.name == "nt" else "clear")

def get_subdomains(domain):
    response = requests.get(f"https://crt.sh/?q=%.{domain}&output=json")
    return {entry['name_value'] for entry in response.json()} if response.status_code == 200 else set()

def save_subdomains_to_file(subdomains, filename):
    folder_path = os.path.join(os.path.dirname(__file__), "subdomains")
    os.makedirs(folder_path, exist_ok=True)
    with open(os.path.join(folder_path, filename), 'w') as f:
        f.write('\n'.join(subdomains))

def main():
    install_required_modules()
    clear_terminal()
    print("DinoSub (Python)\nAuthor: https://github.com/daniisaahir\n")
    domain = input("Target Domain: ")
    subdomains = Pool().submit(get_subdomains, domain).result()
    print(f"Subdomains found: {len(subdomains)}\n{'-' * 36}")
    print(*subdomains, sep='\n')
    if input("Save results to txt file? (y/n): ").lower() == 'y':
        filename = input("Filename: ")
        if not filename.endswith(".txt"):
            filename += ".txt"
        save_subdomains_to_file(subdomains, filename)
        print(f"Saved to subdomains/{filename}.")

if __name__ == "__main__":
    main()
