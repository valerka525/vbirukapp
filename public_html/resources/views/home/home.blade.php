@extends('home.layouts.master')
@section('body')
    <div id="home">

    </div>
    <div id="themes" style="display: none;">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>Theme ID</th>
                    <th>Theme name</th>
                    <th>Last update</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($themes as $theme)
                <tr>
                    <td>{{ $theme['id'] }}</td>
                    <td>{{ $theme['name'] }}</td>
                    <td>{{ $theme['updated_at'] }}</td>
                    <td><a href="{{ route('makeBackup', [$theme['id'], $theme['name']])}}" class="btn btn-warning">Make backup</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div id="backups" style="display: none;">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>Backup theme name</th>
                    <th>Backup created</th>
                    <th>Action</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($backups as $backup)
                    <tr>
                        <td>{{ $backup['name'] }}</td>
                        <td>{{ $backup['created_at'] }}</td>
                        <td><a href="{{ route('themeDelete', $backup) }}" class="btn btn-danger">Delete backup</a></td>
                        <td><a href="{{ route('restoreBackup', $backup) }}" class="btn btn-warning">Restore backup</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div id="schedules" style="display: none;">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>Schedule ID</th>
                    <th>Scheduled theme ID</th>
                    <th>Scheduled theme name</th>
                    <th>Interval of auto-backup (Days)</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule['id'] }}</td>
                        <td>{{ $schedule['theme_id'] }}</td>
                        <td>{{ $schedule['theme_name'] }}</td>
                        <td>{{ $schedule['interval'] }}</td>
                        <td><a href="{{ route('deleteSchedule', $schedule['id']) }}" class="btn btn-danger">Delete schedule</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    <form action="{{ route('addSchedule') }}" method="POST" class="form-inline">
        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Select the theme</label>
        <select name="theme" class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref">
            @foreach($themes as $theme)
                <option value="{{ $theme['id'] }}/{{ $theme['name'] }}">{{ $theme['id'] }} {{ $theme['name'] }} {{ $theme['updated_at'] }}</option>
            @endforeach
        </select>
        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Set an interval (Days)</label>
        <select name="interval" class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
        </select>
        @csrf
        <button type="submit" class="btn btn-warning">Add schedule</button>
    </form>
    </div>
@endsection
