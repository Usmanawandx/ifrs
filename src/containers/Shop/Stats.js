import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Text, ProgressBar } from 'components';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  container: {
    backgroundColor: Colors.white,
    padding: scale(14),
    marginTop: scale(14),
  },
  stats: {
    flex: 1,
    flexDirection: 'row',
  },
  bar: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: scale(8),
  },
  breakdown: {
    marginTop: scale(14),
  },
  rating: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  starContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  divider: {
    borderLeftWidth: 1,
    borderColor: Colors.gray10,
  },
  positive: {
    alignItems: 'center',
    flex: 1,
  },
});

const Stats = ({ shop }) => (
  <View style={styles.container}>
    <View style={styles.stats}>
      <View style={styles.positive}>
        <Text font="h2">{shop.postiveRating}</Text>
        <Text font="h5" color="gray50">Positive Ratings</Text>
      </View>
      <View style={styles.divider} />
      <View style={styles.starContainer}>
        <View style={styles.rating}>
          <Icon
            name="star"
            color={Colors.primary}
            size={scale(30)}
          />
          <Text font="h2">{shop.rating}</Text>
        </View>
        <Text font="h5" color="gray50">Overall Rating</Text>
      </View>
    </View>
    <View style={styles.breakdown}>
      <View style={styles.bar}>
        <Text color="gray75" flex>Product</Text>
        <ProgressBar
          completion={0.9}
        />
      </View>
      <View style={styles.bar}>
        <Text color="gray75" flex>Delivery</Text>
        <ProgressBar
          completion={0.8}
        />
      </View>
      <View style={styles.bar}>
        <Text color="gray75" flex>Communication</Text>
        <ProgressBar
          completion={0.95}
        />
      </View>
    </View>
  </View>
);

Stats.propTypes = {
  shop: PropTypes.object.isRequired,
};

export default Stats;
