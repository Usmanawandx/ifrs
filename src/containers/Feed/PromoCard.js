import React from 'react';
import { View, StyleSheet, ImageBackground } from 'react-native';
import { Card, Text, AvatarGroup } from 'components';
import { scale, verticalScale } from 'react-native-size-matters';

const styles = StyleSheet.create({
  bg: {
    borderRadius: scale(8),
    flex: 1,
  },
  container: {
    overflow: 'hidden',
    borderRadius: scale(8),
    flex: 1,
  },
  content: {
    padding: scale(14),
    backgroundColor: 'rgba(0,0,0,0.2)',
    flex: 1,
  },
  joined: {
    paddingTop: verticalScale(14),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  text: {
    marginLeft: scale(5),
  },
});

const PromoCard = () => (
  <Card style={styles.card}>
    <View style={styles.container}>
      <ImageBackground
        source={require('images/banners/4.jpg')}
        style={styles.bg}
      >
        <View style={styles.content}>
          <Text color="white" font="h2" weight="medium">
            Vouchers Giveaway!
          </Text>
          <Text color="white">
            Stand a chance to win vouchers worth up to $100 to spend at any shop!
          </Text>
          <View style={styles.joined}>
            <AvatarGroup
              size={32}
              show={3}
              label="+30"
              labelSize="h6"
              images={[
                require('images/users/1.jpg'),
                require('images/users/3.jpg'),
                require('images/users/7.jpg'),
                require('images/users/8.jpg'),
              ]}
            />
            <Text color="white" weight="medium" style={styles.text}>have joined.</Text>
          </View>
        </View>
      </ImageBackground>
    </View>
  </Card>
);

export default PromoCard;
