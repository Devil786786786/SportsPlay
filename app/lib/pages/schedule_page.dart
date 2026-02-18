import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class SchedulePage extends StatefulWidget {
  const SchedulePage({super.key});

  @override
  State<SchedulePage> createState() => _SchedulePageState();
}

class _SchedulePageState extends State<SchedulePage>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  final Color backgroundColor = const Color(0xFFF6F4EF);
  final Color accentRed = const Color(0xFF8B1E2D);
  final Color secondaryBlue = const Color(0xFF163A6F);

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        backgroundColor: backgroundColor,
        elevation: 0,
        title: Text(
          "Schedule",
          style: GoogleFonts.inter(
            fontWeight: FontWeight.w700,
            color: Colors.black,
          ),
        ),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: accentRed,
          labelColor: accentRed,
          unselectedLabelColor: Colors.grey,
          tabs: const [
            Tab(text: "Upcoming"),
            Tab(text: "Past"),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [_buildUpcoming(), _buildPast()],
      ),
    );
  }

  /// =============================
  /// UPCOMING EVENTS
  /// =============================
  Widget _buildUpcoming() {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        _dateHeader("Saturday, March 23"),
        _eventCard(
          type: "Game",
          title: "U14 FC Rochester vs Rival FC",
          time: "2:00 PM",
          location: "North Carolina Stadium",
          isGame: true,
        ),
        const SizedBox(height: 16),
        _dateHeader("Tuesday, March 26"),
        _eventCard(
          type: "Practice",
          title: "Team Practice",
          time: "5:30 PM - 7:00 PM",
          location: "City Sports Field",
          isGame: false,
        ),
      ],
    );
  }

  /// =============================
  /// PAST EVENTS
  /// =============================
  Widget _buildPast() {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        _dateHeader("March 16"),
        _eventCard(
          type: "Game",
          title: "FC Rochester vs FC Dakota",
          time: "Final Score: 2 - 1",
          location: "Home Stadium",
          isGame: true,
        ),
        const SizedBox(height: 16),
        _dateHeader("March 12"),
        _eventCard(
          type: "Practice",
          title: "Team Practice",
          time: "Completed",
          location: "City Sports Field",
          isGame: false,
        ),
      ],
    );
  }

  /// =============================
  /// DATE HEADER
  /// =============================
  Widget _dateHeader(String date) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(
        date,
        style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700),
      ),
    );
  }

  /// =============================
  /// EVENT CARD
  /// =============================
  Widget _eventCard({
    required String type,
    required String title,
    required String time,
    required String location,
    required bool isGame,
  }) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Badge
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: isGame ? accentRed : secondaryBlue,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              type,
              style: GoogleFonts.inter(
                color: Colors.white,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(height: 12),
          Text(
            title,
            style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 6),
          Text(time, style: GoogleFonts.inter()),
          const SizedBox(height: 4),
          Text(location, style: GoogleFonts.inter(color: Colors.grey[600])),
        ],
      ),
    );
  }
}
