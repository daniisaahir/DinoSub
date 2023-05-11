import os
import requests
from concurrent.futures import ThreadPoolExecutor
import platform
from pyfiglet import Figlet

def install_required_modules():
    modules = ["requests", "pyfiglet", "setuptools"]

    for module in modules:
        try:
            __import__(module)
        except ImportError:
            os.system(f"pip install {module}")

def clear_terminal():
    if platform.system() == "Windows":
        os.system("cls")
    else:
        os.system("clear")

def get_subdomains(domain):
    url = f"https://crt.sh/?q=%.{domain}&output=json"
    response = requests.get(url)

    if response.status_code == 200:
        data = response.json()
        subdomains = list(set(entry['name_value'] for entry in data))
        return subdomains

    return []

def save_subdomains_to_file(subdomains, filename):
    folder_path = os.path.join(os.path.dirname(__file__), "subdomains")
    os.makedirs(folder_path, exist_ok=True)
    file_path = os.path.join(folder_path, filename)

    with open(file_path, 'w') as file:
        for subdomain in subdomains:
            file.write(subdomain + '\n')

def main():
    install_required_modules()
    clear_terminal()

    # Figlet Banner
    banner = Figlet(font='slant')
    print(banner.renderText("DinoSub"))
    print("Author: https://github.com/daniisaahir\n")

    domain = input("Target Domain: ")
    print(f"Scanning subdomains for {domain}...")

    with ThreadPoolExecutor() as executor:
        future = executor.submit(get_subdomains, domain)
        subdomains = future.result()

    print(f"Subdomains found: {len(subdomains)}")
    print("------------------------------------")
    for subdomain in subdomains:
        print(subdomain)

    save_option = input("Save results to txt file? (y/n): ")
    if save_option.lower() == 'y':
        filename = input("Filename: ")
        if not filename.endswith(".txt"):
            filename += ".txt"
        save_subdomains_to_file(subdomains, filename)
        print(f"Saved to subdomains/{filename}.")

if __name__ == "__main__":
    main()
