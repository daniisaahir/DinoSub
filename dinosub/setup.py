from setuptools import setup, find_packages

setup(
    name='dinosub',
    version='1.2.1',
    author='Danii Saahir',
    description='Subdomains scanner',
    packages=find_packages(),
    install_requires=[
        'requests',
        'setuptools',
    ],
    entry_points={
        'console_scripts': [
            'dinosub = dinosub.main:main',
        ],
    },
)
