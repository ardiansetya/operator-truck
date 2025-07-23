
@extends('layouts.app')

<form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?')">
    @csrf
    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Logout</button>
</form>